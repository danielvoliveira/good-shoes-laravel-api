<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pedido;

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
 
    public function show($id)
    {
        $pedido = Pedido::find($id);
 
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido not found '
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $pedido->toArray()
        ], 400);
    }
 
    public function store(Request $request)
    {
        $this->validate($request, [
            'cliente_id' => 'required',
            'vendedor_id' => 'required',
            'item_pedido' => 'required',
            'data_pedido' => 'required'
        ]);
 
        $pedido = new Pedido();
        $pedido->cliente_id = $request->cliente_id;
        $pedido->vendedor_id = $request->vendedor_id;
        $pedido->data_pedido = $request->data_pedido;
 
        if (auth()->user()->pedido()->save($pedido)) {

            $pedido->item_pedido()->sync($request->item_pedido);

            $pedido->item_pedido = $pedido->item_pedido()->get();

            return response()->json([
                'success' => true,
                'data' => $pedido->toArray()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pedido not added'
            ], 500);
        }
    }
 
    public function update(Request $request, $id)
    {
        $pedido = auth()->user()->pedido()->find($id);
 
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido not found'
            ], 400);
        }
 
        $updated = $pedido->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Pedido can not be updated'
            ], 500);
    }
 
    public function destroy($id)
    {
        $pedido = auth()->user()->pedido()->find($id);
 
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido not found'
            ], 400);
        }
 
        if ($pedido->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pedido can not be deleted'
            ], 500);
        }
    }
}
