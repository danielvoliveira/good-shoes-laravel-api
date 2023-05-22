<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LogAccessController;

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);

Route::middleware('auth:api')->group(function (){
	//Rota para logout
	Route::post('logout', [PassportAuthController::class, 'logout']);

	//Rota para logaccess
	Route::resource('logaccess', LogAccessController::class);

	//Rotas de post
    Route::resource('posts', PostController::class);

    //Rotas de vendedor
    Route::resource('vendedor', VendedorController::class);

    //Rotas de cores
	Route::resource('cor', CorController::class);

    //Rotas de produto
    Route::resource('produto', ProdutoController::class);
	Route::get('produto/restore/{id}', [ProdutoController::class, 'restore']);

	//Rotas de cliente
    Route::resource('cliente', ClienteController::class);

    //Rotas de lote
    Route::resource('lote', LoteController::class);

    //Rotas de pedido e item pedido
    Route::resource('pedido', PedidoController::class);
    Route::resource('item_pedido', ItemPedidoController::class);

    //Rotas de relatorio
    Route::post('pedido/relatorio/', [PedidoController::class, 'relatorio']);
});