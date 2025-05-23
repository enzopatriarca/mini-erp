<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CupomController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\WebhookController;
use App\Services\CepService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// home → lista de produtos
Route::get('/', fn() => redirect()->route('produtos.index'));

// CRUD de produtos
Route::resource('produtos', ProdutoController::class);

// CRUD de cupons (sem show)
Route::resource('cupons', CupomController::class)->except('show');

// Carrinho
Route::get ( 'carrinho',            [CarrinhoController::class, 'index']  )->name('carrinho.index');
Route::post('carrinho/adicionar',   [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');

// Finalizar pedido
Route::post('pedido/finalizar',     [PedidoController::class,   'finalizar'])->name('pedido.finalizar');

// Webhook externo de status
Route::post('webhook/status',       [WebhookController::class,  'status'])->name('webhook.status');

// ViaCEP usando seu CepService via closure
Route::get('viacep/{cep}', function(string $cep, CepService $cepService){
    return response()->json($cepService->buscar($cep));
})->name('viacep.buscar');

Route::resource('pedidos', PedidoController::class)
     ->only(['index','show']);