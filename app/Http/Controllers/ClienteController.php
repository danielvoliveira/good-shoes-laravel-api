<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
 
        if (auth()->user()->cliente()->save($cliente))
            return response()->json([
                'success' => true,
                'data' => $cliente->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Cliente not added'
            ], 500);
    }
 
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
 
        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente not found'
            ], 400);
        }
 
        $updated = $cliente->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Cliente can not be updated'
            ], 500);
    }
 
    public function destroy($id)
    {
        $cliente = Cliente::find($id);
 
        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente not found'
            ], 400);
        }
 
        if ($cliente->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cliente can not be deleted'
            ], 500);
        }
    }
}