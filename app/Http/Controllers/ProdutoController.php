<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::all();
 
        return response()->json([
            'success' => true,
            'data' => $produtos
        ]);
    }
 
    public function show($id)
    {
        $produto = Produto::find($id);
 
        if (!$produto) {
            return response()->json([
                'success' => false,
                'message' => 'Produto not found '
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $produto->toArray()
        ], 400);
    }
 	
    public function restore($id){

        $produto = Produto::withTrashed()->find($id);

        if (!$produto) {
            return response()->json([
                'success' => false,
                'message' => 'Produto not found '
            ], 400);
        }
 
        $produto->restore();

        return response()->json([
            'success' => true,
            'data' => $produto->toArray()
        ], 400);

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required',
            'cor' => 'required',
            'descricao' => 'required'
        ]);
 
        $produto = new Produto();
        $produto->fill($request->all());
 
        if (auth()->user()->produto()->save($produto))
            return response()->json([
                'success' => true,
                'data' => $produto->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Produto not added'
            ], 500);
    }
 
    public function update(Request $request, $id)
    {
        $produto = Produto::find($id);
 
        if (!$produto) {
            return response()->json([
                'success' => false,
                'message' => 'Produto not found'
            ], 400);
        }
 
        $updated = $produto->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Produto can not be updated'
            ], 500);
    }
 
    public function destroy($id)
    {
        $produto = Produto::find($id);
 
        if (!$produto) {
            return response()->json([
                'success' => false,
                'message' => 'Produto not found'
            ], 400);
        }
 
        if ($produto->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Produto can not be deleted'
            ], 500);
        }
    }
}