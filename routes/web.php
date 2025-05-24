<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CupomController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\WebhookController;
use App\Services\CepService;
use Illuminate\Support\Facades\Mail;

Route::get('/', fn() => redirect()->route('produtos.index'));

Route::resource('produtos', ProdutoController::class);

Route::resource('cupons', CupomController::class)->parameters(['cupons' => 'cupom'])->except('show');

Route::get('carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');

Route::post('carrinho/adicionar', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');

Route::post('carrinho/atualizar', [CarrinhoController::class, 'atualizar'])->name('carrinho.atualizar');

Route::post('carrinho/remover', [CarrinhoController::class, 'remover'])->name('carrinho.remover');

Route::post('pedido/finalizar',     [PedidoController::class,   'finalizar'])->name('pedido.finalizar');

Route::get('pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');

Route::get('viacep/{cep}', function(string $cep, CepService $cepService) {
    return response()->json($cepService->buscar($cep));
})->name('viacep.buscar');

Route::resource('pedidos', PedidoController::class)
     ->only(['index','show']);