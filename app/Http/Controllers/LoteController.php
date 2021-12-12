<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Produto;

class LoteController extends Controller
{
    public function index()
    {
        $lotes = Lote::all();
 
        return response()->json([
            'success' => true,
            'data' => $lotes
        ]);
    }
 
    public function show($id)
    {
        //$lote = auth()->user()->lote()->find($id);
        $lote = Lote::find($id);
 
        if (!$lote) {
            return response()->json([
                'success' => false,
                'message' => 'Lote not found '
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $lote->toArray()
        ], 400);
    }
 
    public function store(Request $request)
    {
        $this->validate($request, [
            'produto_id' => 'required',
            'data_fabricacao' => 'required',
            'quantidade_fabricada' => 'required',
            'valor_unitario' => 'required'
        ]);
 
        if(!Produto::find($request->produto_id)){
            return response()->json([
                'success' => false,
                'message' => 'Product for create Lote not found'
            ], 500);
        }

        $lote = new Lote();
        $lote->produto_id = $request->produto_id;
        $lote->data_fabricacao = $request->data_fabricacao;
        $lote->quantidade_fabricada = $request->quantidade_fabricada;
        $lote->quantidade_disponivel = $request->quantidade_fabricada;
        $lote->valor_unitario = $request->valor_unitario;
 
        if (auth()->user()->lote()->save($lote))
            return response()->json([
                'success' => true,
                'data' => $lote->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Lote not added'
            ], 500);
    }
 
    public function update(Request $request, $id)
    {
        if(!Produto::find($request->produto_id)){
            return response()->json([
                'success' => false,
                'message' => 'Product for update Lote not found'
            ], 500);
        }

        $lote = Lote::find($id);
 
        if (!$lote) {
            return response()->json([
                'success' => false,
                'message' => 'Lote not found'
            ], 400);
        }
 
        $updated = $lote->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Lote can not be updated'
            ], 500);
    }
 
    public function destroy($id)
    {
        $lote = Lote::find($id);
 
        if (!$lote) {
            return response()->json([
                'success' => false,
                'message' => 'Lote not found'
            ], 400);
        }
 
        if ($lote->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Lote can not be deleted'
            ], 500);
        }
    }
}
