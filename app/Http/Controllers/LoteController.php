<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Produto;
use App\Repositories\Util;

class LoteController extends Controller
{
    public function index(){
        $lotes = Lote::all();
 
        return response()->json([
            'success' => true,
            'data' => $lotes
        ]);
    }
 
    public function show($id){
        $lote = Lote::find($id);
 
        if (!$lote) {
            return response()->json([
                'success' => false,
                'message' => 'Lot not found'
            ], 400);
        }

        //Buscando o produto do respectivo lote
        $lote_produto = Produto::find($lote->produto_id);
 
        return response()->json([
            'success' => true,
            'data' => $lote->toArray(),
            'product' => $lote_produto->toArray()
        ], 400);
    }
 
    public function store(Request $request){
        $this->validate($request, [
            'produto_id' => 'required',
            'data_fabricacao' => 'required',
            'quantidade_fabricada' => 'required',
            'valor_unitario' => 'required'
        ]);

        $lote = new Lote();
        $lote->produto_id = $request->produto_id;
        $lote->data_fabricacao = $request->data_fabricacao;
        $lote->quantidade_fabricada = $request->quantidade_fabricada;
        $lote->quantidade_disponivel = $request->quantidade_fabricada;
        $lote->valor_unitario = $request->valor_unitario;

        $util = new Util();

        //Verificando se o produto existe
        $produto = Produto::find($lote->produto_id);
        if(!$produto){
            return response()->json([
                'success' => false,
                'message' => 'Product for create Lot not found'
            ], 500);
        }

        //Validando Data de Fabricação
        $validatyDate = $util->validatyDate($lote->data_fabricacao);

        if(!$util->verifySuccess($validatyDate)){
            return $validatyDate;
        }

        $array_validatyDate = $util->jsonToArray($validatyDate);

        //Validando Quantidade Fabricada
        if(!is_int($lote->quantidade_fabricada)){
            return response()->json([
                'success' => false,
                'message' => 'Manufactured quantity must be integer'
            ], 500);
        }else if($lote->quantidade_fabricada < 1){
            return response()->json([
                'success' => false,
                'message' => 'Manufactured quantity must be greater than zero'
            ], 500);
        }

        //Validando Valor Unitário
        if(!is_numeric($lote->valor_unitario)){
            return response()->json([
                'success' => false,
                'message' => 'Unit value must be numeric'
            ], 500);
        }else if($lote->valor_unitario < 0.1){
            return response()->json([
                'success' => false,
                'message' => 'Unit value must be greater than zero'
            ], 500);
        }

        $lote->data_fabricacao = $array_validatyDate["date"];
 
        if (auth()->user()->lote()->save($lote)){
            return response()->json([
                'success' => true,
                'message' => 'Lot added successfully',
                'data' => $lote->toArray(),
                'product' => $produto->toArray()
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Lot not added'
            ], 500);
        }
    }
 
    public function update(Request $request, $id){

        $array_validatyDate = null;

        $produto = Produto::find($request->produto_id);

        //Verifica se o produto enviado existe
        if(!$produto){
            return response()->json([
                'success' => false,
                'message' => 'Product for update Lot not found'
            ], 500);
        }

        $lote = Lote::find($id);

        $util = new Util();
 
        //Verifica se o lote enviado existe
        if (!$lote) {
            return response()->json([
                'success' => false,
                'message' => 'Lot not found'
            ], 400);
        }

        //Validando Data de Fabricação enviada
        if($request->data_fabricacao){
            $validatyDate = $util->validatyDate($request->data_fabricacao);

            if(!$util->verifySuccess($validatyDate)){
                return $validatyDate;
            }

            $array_validatyDate = $util->jsonToArray($validatyDate);
        }

        //Verifica se a quantidade fabricada é inteira e maior que zero
        if($request->quantidade_fabricada){
            if(!is_int($request->quantidade_fabricada)){
                return response()->json([
                    'success' => false,
                    'message' => 'Manufactured quantity must be integer'
                ], 500);
            }else if($request->quantidade_fabricada < 1){
                return response()->json([
                    'success' => false,
                    'message' => 'Manufactured quantity must be greater than zero'
                ], 500);
            }
        }

        //Verifica se a quantidade disponível é inteira
        if($request->quantidade_disponivel){
            if(!is_int($request->quantidade_disponivel)){
                return response()->json([
                    'success' => false,
                    'message' => 'Available quantity must be integer'
                ], 500);
            }
        }

        //Verifica se a quantidade disponível é maior que a quantidade fabricada
        if($request->quantidade_fabricada && $request->quantidade_disponivel){
            if($request->quantidade_fabricada < $request->quantidade_disponivel){
                return response()->json([
                    'success' => false,
                    'message' => 'Sended available quantity is greater than manufactured quantity',
                    'quantidade_fabricada' => $request->quantidade_fabricada,
                    'quantidade_disponivel' => $request->quantidade_disponivel
                ], 400);
            }
        }

        //Verifica se a quantidade disponível enviada é maior que a quantidade fabricada já cadastrada
        if(!$request->quantidade_fabricada && $request->quantidade_disponivel){
            if($lote->quantidade_fabricada < $request->quantidade_disponivel){
                return response()->json([
                    'success' => false,
                    'message' => 'Available quantity is greater than current manufactured quantity',
                    'quantidade_fabricada' => $lote->quantidade_fabricada,
                    'quantidade_disponivel' => $request->quantidade_disponivel
                ], 400);
            }
        }

        //Validando Valor Unitário
        if($request->valor_unitario){
            if(!is_numeric($request->valor_unitario)){
                return response()->json([
                    'success' => false,
                    'message' => 'Unit value must be numeric'
                ], 500);
            }else if($request->valor_unitario < 0.1){
                return response()->json([
                    'success' => false,
                    'message' => 'Unit value must be greater than zero'
                ], 500);
            }
        }
 
        $updated = $lote->fill($request->all())->save();

        if($request->data_fabricacao){
            $lote->data_fabricacao = $array_validatyDate["date"];
            $lote->save();
        }
 
        if ($updated){
            $lote = Lote::find($id);

            return response()->json([
                'success' => true,
                'message' => 'Lote updated',
                'lote_updated' => $lote->toArray(),
                'product' => $produto->toArray()
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Lote can not be updated'
            ], 500);
        }
    }
 
    public function destroy($id){
        $lote = Lote::find($id);
 
        if (!$lote) {
            return response()->json([
                'success' => false,
                'message' => 'Lote not found'
            ], 400);
        }
 
        if ($lote->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Lote deleted'
            ], 500);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Lote can not be deleted'
            ], 500);
        }
    }
}
