<?php

namespace App\Http\Controllers;

use App\Http\Requests\PedidoRequest;
use App\Mail\PedidoConfirmado;
use App\Models\Pedido;
use App\Services\CartService;
use App\Services\CepService;
use Illuminate\Support\Facades\Mail;

class PedidoController extends Controller
{
    public function finalizar(PedidoRequest $r, CartService $cart, CepService $cep)
    {
        $it = session('carrinho', []);
        $subtotal = collect($it)->sum(fn (array $item): float => $item['quantidade'] * $item['preco_unitario']
        );

        $cart->aplicarCupom($subtotal, $r->cupom);

        $frete = $cart->calcularFrete($subtotal);
        $endereco = $cep->buscar($r->cep);

        $pedido = Pedido::create([
            'itens' => $it,
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $subtotal + $frete,
            'cep' => $r->cep,
            'endereco' => "{$endereco['logradouro']}, {$endereco['bairro']}, {$endereco['localidade']}",
        ]);

        Mail::to($r->email)
            ->queue(new PedidoConfirmado($pedido));

        session()->forget('carrinho');

        return view('pedidos.checkout', compact('pedido'));
    }
}
