<?php

use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('produtos', ProdutoController::class)->except('show', 'destroy');
Route::post('carrinho/adicionar', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
Route::post('pedido/finalizar', [PedidoController::class, 'finalizar'])->name('pedido.finalizar');
Route::post('webhook/status', [WebhookController::class, 'status'])->middleware('throttle:60,1');
