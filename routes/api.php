<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ItemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);

/*
Com a estrutura abaixo conseguimos criar todas as rotas do controler sem precisar declarar uma a uma, genial ;D
*/
Route::middleware('auth:api')->group(function () {
    Route::resource('posts', PostController::class);
    Route::resource('vendedor', VendedorController::class);
    Route::resource('produto', ProdutoController::class);

	Route::get('produto/restore/{id}', [ProdutoController::class, 'restore']);    

    Route::resource('cliente', ClienteController::class);
    Route::resource('lote', LoteController::class);
    Route::resource('pedido', PedidoController::class);
    Route::resource('item_pedido', ItemPedidoController::class);
});