<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
 
        if (auth()->user()->vendedor()->save($vendedor))
            return response()->json([
                'success' => true,
                'data' => $vendedor->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Vendedor not added'
            ], 500);
    }
 
    public function update(Request $request, $id)
    {
        $vendedor = Vendedor::find($id);
 
        if (!$vendedor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendedor not found'
            ], 400);
        }
 
        $updated = $vendedor->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Vendedor can not be updated'
            ], 500);
    }
 
    public function destroy($id)
    {
        $vendedor = Vendedor::find($id);
 
        if (!$vendedor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendedor not found'
            ], 400);
        }
 
        if ($vendedor->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vendedor can not be deleted'
            ], 500);
        }
    }
}