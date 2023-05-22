<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Util;

class VendedorController extends Controller
{
    use RefreshDatabase;

    public function index()
    {
        $vendedores = Vendedor::all();
 
        return response()->json([
            'success' => true,
            'data' => $vendedores
        ]);
    }
 
    public function show($id)
    {
        $vendedor = Vendedor::find($id);
 
        if (!$vendedor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendedor not found'
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $vendedor->toArray()
        ], 400);
    }
 	
    public function store(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required',
            'data_contratacao' => 'required'
        ]);
 
        $vendedor = new Vendedor();
        $vendedor->nome = $request->nome;
        $vendedor->data_contratacao = $request->data_contratacao;

        $util = new Util();

        //Validando Nome
        $validatyName = $util->validatyName($vendedor->nome);

        if(!$util->verifySuccess($validatyName)){
            return $validatyName;
        }

        $array_validatyName = $util->jsonToArray($validatyName);
        
        //Validando Data
        $validatyDate = $util->validatyDate($vendedor->data_contratacao);

        if(!$util->verifySuccess($validatyDate)){
            return $validatyDate;
        }

        $array_validatyDate = $util->jsonToArray($validatyDate);

        //Recebendo os valores tratados
        $vendedor->nome = $array_validatyName["name"];
        $vendedor->data_contratacao = $array_validatyDate["date"];

        if (auth()->user()->vendedor()->save($vendedor)){
            return response()->json([
                'success' => true,
                'message' => 'Seller registered successfully',
                'data' => $vendedor->toArray()
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Seller not added'
            ], 500);
        }
    }
 
    public function update(Request $request, $id)
    {
        $vendedor = Vendedor::find($id);
        $array_validatyName = null;
        $array_validatyDate = null;

        $util = new Util();
 
        if (!$vendedor) {
            return response()->json([
                'success' => false,
                'message' => 'Seller not found'
            ], 400);
        }

        if($request->nome){
            $validatyName = $util->validatyName($request->nome);

            if(!$util->verifySuccess($validatyName)){
                return $validatyName;
            }

            $array_validatyName = $util->jsonToArray($validatyName);
        }

        if($request->data_contratacao){

            $validatyDate = $util->validatyDate($request->data_contratacao);

            if(!$util->verifySuccess($validatyDate)){
                return $validatyDate;
            }

            $array_validatyDate = $util->jsonToArray($validatyDate);
        }
 
        $updated = $vendedor->fill($request->all())->save();

        if($request->nome){
            $vendedor->nome = $array_validatyName["name"];
            $vendedor->save();
        }

        if($request->data_contratacao){
            $vendedor->data_contratacao = $array_validatyDate["date"];
            $vendedor->save();
        }
 
        if ($updated){
            return response()->json([
                'success' => true,
                'message' => 'Seller successfully updated'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Seller can not be updated'
            ], 500);
        }
    }
 
    public function destroy($id)
    {
        $vendedor = Vendedor::find($id);
 
        if (!$vendedor) {
            return response()->json([
                'success' => false,
                'message' => 'Seller not found'
            ], 400);
        }
 
        if ($vendedor->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Seller successfully deleted'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Seller can not be deleted'
            ], 500);
        }
    }
}