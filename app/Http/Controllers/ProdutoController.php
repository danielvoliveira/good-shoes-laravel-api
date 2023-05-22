<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Cor;

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
                'message' => 'Product not found'
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
                'message' => 'Product not found '
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
            'cor_id' => 'required',
            'descricao' => 'required',
            'imagem' => 'required|file|mimes:png,jpeg',
        ]);

        //Validando Cor
        $cor = Cor::find($request->cor_id);

        if (!$cor){
            return response()->json([
                'success' => false,
                'message' => 'Collor selected not found'
            ], 400);
        }

        //Salvando imagem no diretório
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/produtos', 'public');
 
        $produto = new Produto();

        $produto->nome = $request->nome;
        $produto->cor_id = $request->cor_id;
        $produto->descricao = $request->descricao;
        $produto->imagem = $imagem_urn;
 
        if (auth()->user()->produto()->save($produto)){
            return response()->json([
                'success' => true,
                'message' => 'Produto added successfuly',
                'data' => $produto->toArray()
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Produto not added'
            ], 500);
        }
    }
 
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'imagem' => 'file|mimes:png,jpeg',
        ]);

        $produto = Produto::find($id);
 
        //Validando o produto
        if (!$produto) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 400);
        }

        //Validando Cor
        if($request->cor_id){
            $cor = Cor::find($request->cor_id);

            if (!$cor){
                return response()->json([
                    'success' => false,
                    'message' => 'Collor selected not found'
                ], 400);
            }
        }
 
        $updated = $produto->fill($request->all())->save();

        //Cadastrando nova imagem caso tenha sido enviada
        if($request->file('imagem')){
            //Removendo imagem antiga
            Storage::disk('public')->delete($produto->imagem);

            //Cadastrando nova imagem
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/produtos', 'public');

            //Salvando novo caminho
            $produto->imagem = $imagem_urn;
            $produto->save();
        }
 
        if ($updated){
            return response()->json([
                'success' => true,
                'message' => 'Produto updated successfuly',
                'produto' => $produto->toArray()
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Produto can not be updated'
            ], 500);
        }
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
            //Removendo imagem
            Storage::disk('public')->delete($produto->imagem);

            return response()->json([
                'success' => true,
                'message' => 'Produto deleted successfuly'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Produto can not be deleted'
            ], 500);
        }
    }
}