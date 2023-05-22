<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Repositories\Util;

class ClienteController extends Controller
{
    use RefreshDatabase;

    public function index()
    {
        $clientes = Cliente::all();
 
        return response()->json([
            'success' => true,
            'data' => $clientes
        ]);
    }
 
    public function show($id)
    {
        $cliente = Cliente::find($id);
 
        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente not found '
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $cliente->toArray()
        ], 400);
    }
 	
    public function store(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required',
            'cpf' => 'required',
            'data_nascimento' => 'required'
        ]);
 
        $cliente = new Cliente();
        $cliente->nome = $request->nome;
        $cliente->cpf = $request->cpf;
        $cliente->data_nascimento = $request->data_nascimento;

        $util = new Util();

        //Validando Nome

        $validatyName = $util->validatyName($cliente->nome);

        if(!$util->verifySuccess($validatyName)){
            return $validatyName;
        }

        $array_validatyName = $util->jsonToArray($validatyName);

        //Verificando a disponibilidade do CPF

        $disponibilityCPF = $util->disponibilityCPF($cliente->cpf);

        if(!$util->verifySuccess($disponibilityCPF)){
            return $disponibilityCPF;
        }

        $array_disponibilityCPF = $util->jsonToArray($disponibilityCPF);

        //Verificando a validade do CPF

        $validityCPF = $util->validityCPF($cliente->cpf);

        if(!$util->verifySuccess($validityCPF)){
            return $validityCPF;
        }

        $array_validityCPF = $util->jsonToArray($validityCPF);

        //Validando Data

        $validatyDate = $util->validatyDate($cliente->data_nascimento);

        if(!$util->verifySuccess($validatyDate)){
            return $validatyDate;
        }

        $array_validatyDate = $util->jsonToArray($validatyDate);

        $cliente->nome = $array_validatyName["name"];
        $cliente->cpf = $array_validityCPF["cpf"];
        $cliente->data_nascimento = $array_validatyDate["date"];

        if (auth()->user()->cliente()->save($cliente)){
            return response()->json([
                'success' => true,
                'message' => 'Client registered successfully',
                'data' => $cliente->toArray()
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Client can not be added'
            ], 500);
        }
    }
 
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        $array_validityCPF = null;
        $array_validatyName = null;
        $array_validatyDate = null;

        $util = new Util();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 400);
        }

        if($request->nome){
            $validatyName = $util->validatyName($request->nome);

            if(!$util->verifySuccess($validatyName)){
                return $validatyName;
            }

            $array_validatyName = $util->jsonToArray($validatyName);
        }

        //Se a request CPF existe, ele verifica antes de atualizar

        if($request->cpf){

            $request->cpf = preg_replace( '/[^0-9]/is', '', $request->cpf);

            if($cliente->cpf != $request->cpf) {
                $disponibilityCPF = $util->disponibilityCPF($request->cpf);

                if(!$util->verifySuccess($disponibilityCPF)){
                    return $disponibilityCPF;
                }

                $array_disponibilityCPF = $util->jsonToArray($disponibilityCPF);
            }
                
            $validityCPF = $util->validityCPF($request->cpf);

            if(!$util->verifySuccess($validityCPF)){
                return $validityCPF;
            }

            $array_validityCPF = $util->jsonToArray($validityCPF);
        }

        //Se a request Data existe, ele verifica antes de atualizar

        if($request->data_nascimento){

            $validatyDate = $util->validatyDate($request->data_nascimento);

            if(!$util->verifySuccess($validatyDate)){
                return $validatyDate;
            }

            $array_validatyDate = $util->jsonToArray($validatyDate);
        }

        $updated = $cliente->fill($request->all())->save();

        //Insere os campos tratados caso eles existam
        if($request->cpf){
            $cliente->cpf = $array_validityCPF["cpf"];
            $cliente->save();
        }

        if($request->nome){
            $cliente->nome = $array_validatyName["name"];
            $cliente->save();
        }

        if($request->data_nascimento){
            $cliente->data_nascimento = $array_validatyDate["date"];
            $cliente->save();
        }

        if ($updated){
            return response()->json([
                'success' => true,
                'message' => 'Client successfully updated'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Client can not be updated'
            ], 500);
        }
    }
 
    public function destroy($id)
    {
        $cliente = Cliente::find($id);
 
        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 400);
        }
 
        if ($cliente->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Client successfully deleted'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Client can not be deleted'
            ], 500);
        }
    }
}