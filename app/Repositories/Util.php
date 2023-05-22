<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Lote;

class Util {

	//Verificar sucesso de um Json no padrão do sistema
	public function verifySuccess($json){
		$array = $this->jsonToArray($json);

		if($array["success"]){
            return true;
        }else{
        	return false;
        }
	}

	//Funçaõ para transformar Json em Array
	public function jsonToArray($json){
		return json_decode($json->getContent(), true);
	}

	//Função para validação de nome
	public function validatyName($nome) {
        $nome_tratado = "";
        $nome = strtolower($nome); // Converter o nome todo para minúsculo
        $nome = explode(" ", $nome); // Separa o nome por espaços

        if(count($nome) == 1){
            return response()->json([
                'success' => false,
                'message' => 'Fill in the full name'
            ], 500);
        }

        // Tratar cada palavra do nome
        $array_de_tratamento = ["de", "da", "e", "da", "dos", "do", "das"];

        if(count($nome) == 2){
            for ($i=0; $i < count($nome); $i++) {

                if (in_array($nome[$i], $array_de_tratamento)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Fill in the full name. '". ucfirst($nome[$i]) . "' is not a last name."
                    ], 500);
                }
            }
        }

        for ($i=0; $i < count($nome); $i++) {
            // Tratar cada palavra do nome
            if (in_array($nome[$i], $array_de_tratamento)) {
                $nome_tratado .= $nome[$i].' '; // Se a palavra estiver dentro das complementares mostrar toda em minúsculo
            }else {
                $nome_tratado .= ucfirst($nome[$i]).' '; // Se for um nome, mostrar a primeira letra maiúscula
            }

        }

        return response()->json([
            'success' => true,
            'message' => 'Full name validated',
            'name' => trim($nome_tratado)
        ], 500);
    }

    //Função para validação de data
    public function validatyDate($data){

        $data = $data;

        if(str_contains($data, "/")){
            $data = str_replace("/", "-", $data);
        }

        if(str_contains($data, ".")){
            $data = str_replace(".", "-", $data);
        }

        $array_data = explode("-", $data);

        if(count($array_data) == 3){
            //Validando data padrão americano
            if(strlen($array_data[0]) == 4 && strlen($array_data[1]) == 2 && strlen($array_data[2]) == 2){
                if(intval($array_data[1]) > 0 && intval($array_data[1]) <= 12 && intval($array_data[2]) > 0 && intval($array_data[2]) <= 31){
                    return response()->json([
                        'success' => true,
                        'message' => 'Date validated successfully',
                        'date' => $data
                    ], 500);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid date',
                        'date' => $data
                    ], 500);
                }
            //Validando data padrão brasileiro
            }else if(strlen($array_data[0]) == 2 && strlen($array_data[1]) == 2 && strlen($array_data[2]) == 4){
                if(intval($array_data[0]) > 0 && intval($array_data[0]) <= 31 && intval($array_data[1]) > 0 && intval($array_data[1]) <= 12){
                    $data = date('Y-m-d', strtotime($data));
                    return response()->json([
                        'success' => true,
                        'message' => 'Date validated successfully',
                        'date' => $data
                    ], 500);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid date',
                        'date' => $data
                    ], 500);
                }
            }else{
                //Data com padrão invalido
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date',
                    'date' => $data
                ], 500);
            }
        }else{
            //Data com padrão invalido
            return response()->json([
                'success' => false,
                'message' => 'Invalid date',
                'date' => $data
            ], 500);
        }
    }

    //Verifica a disponibilidade de um CPF na tabela Clientes
    public function disponibilityCPF($cpf){

        $cpf = preg_replace( '/[^0-9]/is', '', $cpf);
        
        // Verifica se o CPF já foi cadastrado
        $cliente = DB::table('clientes')->where('cpf', '=', $cpf)->get();

        if(count($cliente)==0){
            return response()->json([
                'success' => true,
                'message' => 'CPF not registered'
            ], 500);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Already registered CPF'
            ], 500);
        }
    }

    //Verifica se o CPF é valido
    public function validityCPF($cpf) {

        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
         
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid CPF'
            ], 500);
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid CPF'
            ], 500);
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid CPF'
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'CPF available',
            'cpf' => $cpf
        ], 500);
    }

    //Verifica a disponibilidade de um lote pela quantidade disponível.
    public function disponibilityLote($lote_id, $quantidade){

    	$lote = Lote::find($lote_id);

        if (!$lote) {
            return response()->json([
                'success' => false,
                'message' => 'Lote not found',
                'id_lote' => $lote_id
            ], 400);
        }

        if($lote['quantidade_disponivel'] < $quantidade){
            return response()->json([
                'success' => false,
                'message' => 'Unavailable quantity in stock',
                'quantidade_disponivel' => $lote['quantidade_disponivel']
            ], 400);
        }else{
            return response()->json([
                'success' => true,
                'message' => 'Quantity available in stock',
                'quantidade_disponivel' => $lote['quantidade_disponivel']
            ], 400);
        }        
    }

    public function devolutionLote($lote_id, $quantidade_devolucao){

        if (!is_int($quantidade_devolucao)) {
            return response()->json([
                'success' => false,
                'message' => 'The quantity must be integer',
                'quantidade_devolucao' => $quantidade_devolucao
            ], 400);
        }

        $lote = Lote::find($lote_id);

        if (!$lote) {
            return response()->json([
                'success' => false,
                'message' => 'Lote not found',
                'id_lote' => $lote_id
            ], 400);
        }

        $quantidade_atualizada = $lote->quantidade_disponivel + $quantidade_devolucao;

        if($quantidade_atualizada > $lote->quantidade_fabricada){
            return response()->json([
                'success' => false,
                'message' => 'Updated quantity is greater than manufactured quantity',
                'quantidade_fabricada' => $lote->quantidade_fabricada,
                'quantidade_atualizada' => $quantidade_atualizada
            ], 400);
        }

        $lote->quantidade_disponivel = $quantidade_atualizada;

        if($lote->save()){
            return response()->json([
                'success' => true,
                'message' => 'Lote updated successfully',
                'quantidade_fabricada' => $lote->quantidade_fabricada,
                'quantidade_atualizada' => $quantidade_atualizada
            ], 400);
        }
    }

    //Função para verificar se existe um lote_id repetido em um array de lotes
    public function verifyLoteRepetido(array $item_pedido){
        $item_pedido_validty = $item_pedido;
        $lote_repetido = [];
        $resultado_verificacao = 0;
        for($x=0; $x<count($item_pedido); $x++){
            $verificador = 0;
            for($y=0; $y<count($item_pedido_validty); $y++){
                if($item_pedido[$x]['lote_id'] == $item_pedido_validty[$y]['lote_id']){
                    $verificador++; 
                }
            }

            if($verificador>1){
                //Lógica para remover duplicação dentro do $lote_repetido[]
                if(count($lote_repetido) == 0){
                    $lote_repetido[0] = $item_pedido[$x]['lote_id'];
                }

                if(count($lote_repetido) == 1){
                    if($lote_repetido[0] != $item_pedido[$x]['lote_id']){
                        $lote_repetido[1] = $item_pedido[$x]['lote_id'];
                    }
                }

                if(count($lote_repetido) > 1){

                    $verificador_lote_repetido = 0;

                    for($z=0; $z < count($lote_repetido); $z++){
                        if($lote_repetido[$z] == $item_pedido[$x]['lote_id']){
                            $verificador_lote_repetido++;
                        }
                    }
                    if($verificador_lote_repetido == 0){
                        $lote_repetido[count($lote_repetido)] = $item_pedido[$x]['lote_id'];
                    }
                }
                $resultado_verificacao++;
            }
        }
        
        if($resultado_verificacao>0){
            return response()->json([
                'success' => false,
                'message' => 'Selected order items cannot be the same',
                'repeated_codes' => $lote_repetido
            ], 500);
        }else{
            return response()->json([
                'success' => true,
                'message' => 'Selected order items are not the same'
            ], 500);
        }
    }
}

?>