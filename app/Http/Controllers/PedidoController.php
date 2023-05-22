<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Vendedor;
use App\Models\Lote;
use App\Models\ItemPedido;
use App\Repositories\Util;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with('item_pedido')->get();
 
        return response()->json([
            'success' => true,
            'data' => $pedidos
        ]);
    }
 
    public function store(Request $request)
    {
        $this->validate($request, [
            'cliente_id' => 'required',
            'vendedor_id' => 'required',
            'item_pedido' => 'required',
            'data_pedido' => 'required'
        ]);

        //Verificando se o cliente existe
        $cliente = Cliente::find($request->cliente_id);
        if(!$cliente){
            return response()->json([
                'success' => false,
                'message' => 'Client selected not found',
                'cliente_id' => $request->cliente_id
            ], 500);
        }

        //Verificando se vendedor existe
        $vendedor = Vendedor::find($request->vendedor_id);
        if(!$vendedor){
            return response()->json([
                'success' => false,
                'message' => 'Seller selected not found',
                'cliente_id' => $request->vendedor_id
            ], 500);
        }

        //Validando data do pedido
        $util = new Util();

        $validatyDate = $util->validatyDate($request->data_pedido);

        if(!$util->verifySuccess($validatyDate)){
            return $validatyDate;
        }

        $array_validatyDate = $util->jsonToArray($validatyDate);

        //Validando Item Pedido
        if(count($request->item_pedido) == 0){
            return response()->json([
                'success' => false,
                'message' => 'You need to select items for your order'
            ], 500);
        }

        //Para pedidos com um único item
        $lote = null;

        if(count($request->item_pedido) == 1){
            $lote = Lote::find($request->item_pedido[0]['lote_id']);

            if(!$lote){
                return response()->json([
                    'success' => false,
                    'message' => 'Lot selected not found',
                    'lote_id' => $request->item_pedido[0]['lote_id']
                ], 500);
            }

            if($lote['quantidade_disponivel'] < $request->item_pedido[0]['quantidade']){
                return response()->json([
                    'success' => false,
                    'message' => 'Unavailable quantity of selected lot',
                    'lote_id' => $request->item_pedido[0]['lote_id'],
                    'quantidade_disponivel' => $lote['quantidade_disponivel']
                ], 500);
            }

            $lote->quantidade_disponivel = $lote->quantidade_disponivel - $request->item_pedido[0]['quantidade'];
            $lote->save();
        }

        //Para pedidos com mais de um item
        if(count($request->item_pedido) > 1){

            //Verificando se os itens enviados tem algum lote_id repetido
            $verificador_lote_repetido = $util->verifyLoteRepetido($request->item_pedido);

            if(!$util->verifySuccess($verificador_lote_repetido)){
                return $verificador_lote_repetido;
            }

            //return $request->item_pedido[0]['lote_id'];
            $lote = [];

            //Primeiro verificamos todos
            for($x=0; $x < count($request->item_pedido); $x++){
                $lote[$x] = Lote::find($request->item_pedido[$x]['lote_id']);

                if(!$lote[$x]){
                    return response()->json([
                        'success' => false,
                        'message' => 'Lot selected not found',
                        'lote_id' => $request->item_pedido[$x]['lote_id']
                    ], 500);
                }

                if($lote[$x]['quantidade_disponivel'] < $request->item_pedido[$x]['quantidade']){
                    return response()->json([
                        'success' => false,
                        'message' => 'Unavailable quantity of selected lot',
                        'lote_id' => $request->item_pedido[$x]['lote_id'],
                        'quantidade_disponivel' => $lote[$x]['quantidade_disponivel']
                    ], 500);
                }
            }

            //Depois salvamos todos caso não hajam exceções
            for($x=0; $x < count($lote); $x++){
                $lote[$x]->quantidade_disponivel = $lote[$x]->quantidade_disponivel - $request->item_pedido[$x]['quantidade'];
                $lote[$x]->save();
            }
        }
 
        $pedido = new Pedido();
        $pedido->cliente_id = $request->cliente_id;
        $pedido->vendedor_id = $request->vendedor_id;
        $pedido->data_pedido = $array_validatyDate['date'];
 
        if (auth()->user()->pedido()->save($pedido)) {

            $pedido->item_pedido()->sync($request->item_pedido);

            $pedido->item_pedido = $pedido->item_pedido()->get();

            return response()->json([
                'success' => true,
                'data' => $pedido->toArray()
            ]);
        }else{
            //Caso de algum problemas, voltamos as quantidades dos lotes

            //Devolução caso tenha apenas um item
            if(count($request->item_pedido) == 1){
                $lote->quantidade_disponivel = $lote->quantidade_disponivel + $request->item_pedido[0]['quantidade'];
                $lote->save();
            }

            //Devolução para mais de um item
            if(count($request->item_pedido) > 1){
                for($x=0; $x < count($lote); $x++){
                    $lote[$x]->quantidade_disponivel = $lote[$x]->quantidade_disponivel + $request->item_pedido[$x]['quantidade'];
                    $lote[$x]->save();
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Pedido not added'
            ], 500);
        }
    }


    public function show($id)
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido not found '
            ], 400);
        }

        $item_pedido = Pedido::find($id)->item_pedido()->get();
 
        return response()->json([
            'success' => true,
            'pedido' => $pedido->toArray(),
            'item_pedido' => $item_pedido->toArray()
        ], 400);
    }
 
    public function update(Request $request, $id){
        $pedido = Pedido::find($id);
 
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido not found'
            ], 400);
        }

        //Verificando se o cliente existe
        if($request->cliente_id){
            $cliente = Cliente::find($request->cliente_id);
            if(!$cliente){
                return response()->json([
                    'success' => false,
                    'message' => 'Client selected not found',
                    'cliente_id' => $request->cliente_id
                ], 500);
            }
        }
        
        //Verificando se vendedor existe
        if($request->vendedor_id){
            $vendedor = Vendedor::find($request->vendedor_id);
            if(!$vendedor){
                return response()->json([
                    'success' => false,
                    'message' => 'Seller selected not found',
                    'cliente_id' => $request->vendedor_id
                ], 500);
            }
        }

        //Validando data do pedido
        if($request->data_pedido){
            $util = new Util();

            $validatyDate = $util->validatyDate($request->data_pedido);

            if(!$util->verifySuccess($validatyDate)){
                return $validatyDate;
            }

            $array_validatyDate = $util->jsonToArray($validatyDate);
        }
        
        //Validando Item Pedido
        if(count($request->item_pedido) > 0){

            //Verificando se os itens enviados tem algum lote_id repetido
            $util = new Util();

            $verificador_lote_repetido = $util->verifyLoteRepetido($request->item_pedido);

            if(!$util->verifySuccess($verificador_lote_repetido)){
                return $verificador_lote_repetido;
            }

            //Primeiro devolvemos os itens de pedido para a tabela lote
            $item_pedido = DB::table('item_pedido')->where('pedido_id', $id)->get();

            if(count($item_pedido) == 1){
                $lote = Lote::find($item_pedido[0]->lote_id);
                $lote->quantidade_disponivel = $lote->quantidade_disponivel + $item_pedido[0]->quantidade;
                $lote->save();
            }

            if(count($item_pedido) > 1){

                for($x=0; $x < count($item_pedido); $x++){
                    $lote = Lote::find($item_pedido[$x]->lote_id);
                    $lote->quantidade_disponivel = $lote->quantidade_disponivel + $item_pedido[$x]->quantidade;
                    $lote->save();
                }
            }

            //Deletando os itens de pedido
            if(count($item_pedido) > 0){
                if(!DB::table('item_pedido')->where('pedido_id', $id)->delete()){
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to delete order items'
                    ], 500);
                }
            }

            //Após devolver os itens de pedido anteriores, inserimos os novos itens
            $lote = null;

            //Para pedidos com um único item
            if(count($request->item_pedido) == 1){
                $lote = Lote::find($request->item_pedido[0]['lote_id']);

                if(!$lote){
                    return response()->json([
                        'success' => false,
                        'message' => 'Lot selected not found',
                        'lote_id' => $request->item_pedido[0]['lote_id']
                    ], 500);
                }

                if($lote['quantidade_disponivel'] < $request->item_pedido[0]['quantidade']){
                    return response()->json([
                        'success' => false,
                        'message' => 'Unavailable quantity of selected lot',
                        'lote_id' => $request->item_pedido[0]['lote_id'],
                        'quantidade_disponivel' => $lote['quantidade_disponivel']
                    ], 500);
                }

                $lote->quantidade_disponivel = $lote->quantidade_disponivel - $request->item_pedido[0]['quantidade'];
                $lote->save();
            }

            //Para pedidos com mais de um item
            if(count($request->item_pedido) > 1){
                $lote = [];

                //Primeiro verificamos todos
                for($x=0; $x < count($request->item_pedido); $x++){
                    $lote[$x] = Lote::find($request->item_pedido[$x]['lote_id']);

                    if(!$lote[$x]){
                        return response()->json([
                            'success' => false,
                            'message' => 'Lot selected not found',
                            'lote_id' => $request->item_pedido[$x]['lote_id']
                        ], 500);
                    }

                    if($lote[$x]['quantidade_disponivel'] < $request->item_pedido[$x]['quantidade']){
                        return response()->json([
                            'success' => false,
                            'message' => 'Unavailable quantity of selected lot',
                            'lote_id' => $request->item_pedido[$x]['lote_id'],
                            'quantidade_disponivel' => $lote[$x]['quantidade_disponivel']
                        ], 500);
                    }
                }

                //Depois salvamos todos caso não hajam exceções
                for($x=0; $x < count($lote); $x++){
                    $lote[$x]->quantidade_disponivel = $lote[$x]->quantidade_disponivel - $request->item_pedido[$x]['quantidade'];
                    $lote[$x]->save();
                }
            }
        }
 
        $updated = $pedido->fill($request->all())->save();

        if ($updated){

            $pedido->item_pedido()->sync($request->item_pedido);

            return response()->json([
                'success' => true,
                'message' => 'Pedido updated sussccesfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pedido can not be updated'
            ], 500);
        }    
    }
 
    public function destroy($id){
        //Verificando se o pedido existe
        $pedido = Pedido::find($id);
 
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido not found'
            ], 400);
        }

        //Primeiro devolvemos os itens de pedido para a tabela lote
        $item_pedido = DB::table('item_pedido')->where('pedido_id', $id)->get();

        if(count($item_pedido) == 1){
            $lote = Lote::find($item_pedido[0]->lote_id);
            $lote->quantidade_disponivel = $lote->quantidade_disponivel + $item_pedido[0]->quantidade;
            $lote->save();
        }

        if(count($item_pedido) > 1){
            for($x=0; $x < count($item_pedido); $x++){
                $lote = Lote::find($item_pedido[$x]->lote_id);
                $lote->quantidade_disponivel = $lote->quantidade_disponivel + $item_pedido[$x]->quantidade;
                $lote->save();
            }
        }

        //Deletando os itens de pedido
        if(count($item_pedido) > 0){
            if(!DB::table('item_pedido')->where('pedido_id', $id)->delete()){
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to delete order items'
                ], 500);
            }
        }
 
        if ($pedido->delete()){
            return response()->json([
                'success' => true,
                'message' => 'Order deleted sussccesfully',
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Order can not be deleted'
            ], 500);
        }
    }

    public function relatorio(Request $request){
        /*Relatório de pedidos:

        - Por valor
        - Por ordem de data

        - Quantidade de pedidos
        - Valor total dos pedidos
        - Data de Início
        - Data de Fim

        - Clientes que mais compraram
        - Vendedores que mais venderam
        - Produtos mais vendidos

        - Listagem dos pedidos

        */

        $this->validate($request, [
            'data_inicio' => 'required',
            'data_final' => 'required',
            'tipo' => 'required',
            'ordem' => 'required'
        ]);

        $util = new Util();

        //Validando data início
        $validatyDataInicio = $util->validatyDate($request->data_inicio);

        if(!$util->verifySuccess($validatyDataInicio)){
            return $validatyDataInicio;
        }

        $array_validatyDataInicio = $util->jsonToArray($validatyDataInicio);

        $data_inicio = $array_validatyDataInicio['date'];

        //Validando data final
        $validatyDataFinal = $util->validatyDate($request->data_final);

        if(!$util->verifySuccess($validatyDataFinal)){
            return $validatyDataFinal;
        }

        $array_validatyDataFinal = $util->jsonToArray($validatyDataFinal);

        $data_final = $array_validatyDataFinal['date'];
        
        //Verificando se a data_inicio é menor que a data_final
        if($data_inicio > $data_final){
            return response()->json([
                'success' => false,
                'message' => 'The start date must be less than or equal to the end date'
            ], 500);
        }

        //Validando o tipo: pedido, vendedor, cliente ou produto
        $tipo = strtolower($request->tipo);

        if($tipo != 'pedido'/* && $tipo != 'vendedor' && $tipo != 'cliente' && $tipo != 'produto'*/){
            return response()->json([
                'success' => false,
                'message' => 'The type must be equal to: pedido',
                'tipo_selected' => $tipo
            ], 500);
        }

        //Validando ordem: valor ou data
        $ordem = strtolower($request->ordem);

        if($ordem != 'valor' && $ordem != 'data'){
            return response()->json([
                'success' => false,
                'message' => 'The order must be equal to: valor or data',
                'tipo_selected' => $ordem
            ], 500);
        }

        $pedidos = DB::table('pedidos')
                    ->whereDate('data_pedido', '>=', $data_inicio)
                    ->whereDate('data_pedido', '<=', $data_final)
                    ->orderBy('data_pedido')
                    ->get();

        $relatorio = [];

        $relatorio['resumo'] = [];

        $valor_total_geral = 0;

        $relatorio_pedidos = [];

        //Gerando relatório com apenas um pedido
        if(count($pedidos) == 1){
            $cliente = DB::table('clientes')->where('id', '=', $pedidos[0]->cliente_id)->get();
            $vendedor = DB::table('vendedors')->where('id', '=', $pedidos[0]->vendedor_id)->get();
            $item_pedido = DB::table('item_pedido')->where('pedido_id', '=', $pedidos[0]->id)->get();

            //Resumo de um pedido
            $relatorio_pedidos[0]['pedido'] = [];

            //return $item_pedido;

            $valor_total_pedido = 0;

            if(count($item_pedido) == 1){
                $lote = DB::table('lotes')->where('id', '=', $item_pedido[0]->lote_id)->get();
                $produto = DB::table('produtos')->where('id', '=', $lote[0]->produto_id)->get();
                
                $valor_total_geral = $valor_total_geral + ($item_pedido[0]->quantidade * $lote[0]->valor_unitario);
                $valor_total_pedido = $item_pedido[0]->quantidade * $lote[0]->valor_unitario;

                $relatorio_pedidos[0]['item_pedido'] = [
                    'lote_id' =>  $item_pedido[0]->lote_id,
                    'produto_id' => $lote[0]->produto_id,
                    'produto_nome' => $produto[0]->nome,
                    'quantidade' => $item_pedido[0]->quantidade,
                    'valor_unitario' => $lote[0]->valor_unitario,
                    'valor_total' => $item_pedido[0]->quantidade * $lote[0]->valor_unitario
                ];
            }

            if(count($item_pedido) > 1){

                $multiplos_itens = [];

                for($z=0; $z < count($item_pedido); $z++){

                    $lote = DB::table('lotes')->where('id', '=', $item_pedido[$z]->lote_id)->get();
                    $produto = DB::table('produtos')->where('id', '=', $lote[0]->produto_id)->get();
                    
                    $valor_total_geral = $valor_total_geral + ($item_pedido[$z]->quantidade * $lote[0]->valor_unitario);

                    $valor_total_pedido = $valor_total_pedido + ($item_pedido[$z]->quantidade * $lote[0]->valor_unitario);

                    $multiplos_itens[$z] = [
                        'lote_id' =>  $item_pedido[$z]->lote_id,
                        'produto_id' => $lote[0]->produto_id,
                        'produto_nome' => $produto[0]->nome,
                        'quantidade' => $item_pedido[$z]->quantidade,
                        'valor_unitario' => $lote[0]->valor_unitario,
                        'valor_total' => $item_pedido[$z]->quantidade * $lote[0]->valor_unitario
                    ];

                }

                $relatorio_pedidos[0]['item_pedido'] = $multiplos_itens;
            }

            $relatorio_pedidos[0]['pedido'] = [
                'data' => $pedidos[0]->data_pedido,
                'pedido_id' => $pedidos[0]->id,
                'cliente_id' => $pedidos[0]->cliente_id,
                'cliente_nome' => $cliente[0]->nome,
                'vendedor_id' => $pedidos[0]->vendedor_id,
                'vendedor_nome' => $vendedor[0]->nome,
                'quantidade_itens' => count($item_pedido),
                'valor_total' => $valor_total_pedido
            ];
        }

        //Gerando relatório com mais de um pedido
        if(count($pedidos)>1){
            for($x=0; $x < count($pedidos); $x++){
                $cliente = DB::table('clientes')->where('id', '=', $pedidos[$x]->cliente_id)->get();
                $vendedor = DB::table('vendedors')->where('id', '=', $pedidos[$x]->vendedor_id)->get();
                $item_pedido = DB::table('item_pedido')->where('pedido_id', '=', $pedidos[$x]->id)->get();

                //Resumo de um pedido
                $relatorio_pedidos[$x]['pedido'] = [];

                //return $item_pedido;

                $valor_total_pedido = 0;

                if(count($item_pedido) == 1){
                    $lote = DB::table('lotes')->where('id', '=', $item_pedido[0]->lote_id)->get();
                    $produto = DB::table('produtos')->where('id', '=', $lote[0]->produto_id)->get();
                    
                    $valor_total_geral = $valor_total_geral + ($item_pedido[0]->quantidade * $lote[0]->valor_unitario);
                    $valor_total_pedido = $item_pedido[0]->quantidade * $lote[0]->valor_unitario;

                    $relatorio_pedidos[$x]['item_pedido'] = [
                        'lote_id' =>  $item_pedido[0]->lote_id,
                        'produto_id' => $lote[0]->produto_id,
                        'produto_nome' => $produto[0]->nome,
                        'quantidade' => $item_pedido[0]->quantidade,
                        'valor_unitario' => $lote[0]->valor_unitario,
                        'valor_total' => $item_pedido[0]->quantidade * $lote[0]->valor_unitario
                    ];
                }

                if(count($item_pedido) > 1){

                    $multiplos_itens = [];

                    for($z=0; $z < count($item_pedido); $z++){

                        $lote = DB::table('lotes')->where('id', '=', $item_pedido[$z]->lote_id)->get();
                        $produto = DB::table('produtos')->where('id', '=', $lote[0]->produto_id)->get();
                        
                        $valor_total_geral = $valor_total_geral + ($item_pedido[$z]->quantidade * $lote[0]->valor_unitario);

                        $valor_total_pedido = $valor_total_pedido + ($item_pedido[$z]->quantidade * $lote[0]->valor_unitario);

                        $multiplos_itens[$z] = [
                            'lote_id' =>  $item_pedido[$z]->lote_id,
                            'produto_id' => $lote[0]->produto_id,
                            'produto_nome' => $produto[0]->nome,
                            'quantidade' => $item_pedido[$z]->quantidade,
                            'valor_unitario' => $lote[0]->valor_unitario,
                            'valor_total' => $item_pedido[$z]->quantidade * $lote[0]->valor_unitario
                        ];

                    }

                    $relatorio_pedidos[$x]['item_pedido'] = $multiplos_itens;
                }

                $relatorio_pedidos[$x]['pedido'] = [
                    'data' => $pedidos[$x]->data_pedido,
                    'pedido_id' => $pedidos[$x]->id,
                    'cliente_id' => $pedidos[$x]->cliente_id,
                    'cliente_nome' => $cliente[0]->nome,
                    'vendedor_id' => $pedidos[$x]->vendedor_id,
                    'vendedor_nome' => $vendedor[0]->nome,
                    'quantidade_itens' => count($item_pedido),
                    'valor_total' => $valor_total_pedido
                ];
            }
        }

        $relatorio['resumo'] = [
                'tipo' => $ordem,
                'data_inicio' => $data_inicio,
                'data_final' => $data_final,
                'quantidade_pedidos' => count($pedidos),
                'valor_total' => $valor_total_geral
            ];

        $relatorio['pedidos'] = $relatorio_pedidos;

        //Gerando relatório por ordem de data
        if($ordem == 'data'){
            return $relatorio;
        }

        if($ordem == 'valor'){

            //return $relatorio['pedidos'][0]['pedido']['valor_total'];

            $array_pedidos_simplificado = [];

            for($x=0; $x < count($relatorio['pedidos']); $x++){
                $array_pedidos_simplificado[$x]['posicao_vetor'] =  $x;
                $array_pedidos_simplificado[$x]['pedido_id'] = $relatorio['pedidos'][$x]['pedido']['pedido_id'];
                $array_pedidos_simplificado[$x]['valor_total'] = $relatorio['pedidos'][$x]['pedido']['valor_total'];
            }

            $util = new Util();

            $array_reordenado_suporte = [];

            foreach ($array_pedidos_simplificado as $key => $row) {
                $array_reordenado_suporte[$key] = $row['valor_total'];
            }

            array_multisort($array_reordenado_suporte, SORT_DESC, $array_pedidos_simplificado);

            //return $array_pedidos_simplificado;

            $array_pedidos_valor_final = [];
            for($x=0; $x < count($array_pedidos_simplificado); $x++){
                $array_pedidos_valor_final[$x] = $relatorio['pedidos'][$array_pedidos_simplificado[$x]['posicao_vetor']];
            }

            $relatorio['pedidos'] = $array_pedidos_valor_final;

            return $relatorio;
        }  
    }
}
