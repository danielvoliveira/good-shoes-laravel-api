<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cor;

class CorController extends Controller
{
    public function index()
    {
        $cores = Cor::all();
 
        return response()->json([
            'success' => true,
            'data' => $cores
        ]);
    }
 
    public function show($id)
    {
        $cor = Cor::find($id);
 
        if (!$cor) {
            return response()->json([
                'success' => false,
                'message' => 'Cor not found '
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $cor->toArray()
        ], 400);
    }
 	
    public function restore($id){

        $cor = Cor::withTrashed()->find($id);

        if (!$cor) {
            return response()->json([
                'success' => false,
                'message' => 'Cor not found '
            ], 400);
        }
 
        $cor->restore();

        return response()->json([
            'success' => true,
            'data' => $cor->toArray()
        ], 400);

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required',
            'hexadecimal' => 'required'
        ]);
 
        $cor = new Cor();
        $cor->fill($request->all());
 
        if (auth()->user()->cor()->save($cor)){
            return response()->json([
                'success' => true,
                'message' => 'Cor added successfully',
                'data' => $cor->toArray()
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Cor not added'
            ], 500);
        }
    }
 
    public function update(Request $request, $id)
    {
        $cor = Cor::find($id);
 
        if (!$cor) {
            return response()->json([
                'success' => false,
                'message' => 'Cor not found'
            ], 400);
        }
 
        $updated = $cor->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Cor can not be updated'
            ], 500);
    }
 
    public function destroy($id)
    {
        $cor = Cor::find($id);
 
        if (!$cor) {
            return response()->json([
                'success' => false,
                'message' => 'Cor not found'
            ], 400);
        }
 
        if ($cor->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cor can not be deleted'
            ], 500);
        }
    }
}
