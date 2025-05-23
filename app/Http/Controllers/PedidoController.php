<?php

namespace App\Http\Controllers;

use App\Http\Requests\PedidoRequest;
use App\Mail\PedidoConfirmado;
use App\Models\Pedido;
use App\Services\CartService;
use App\Services\CepService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PedidoController extends Controller
{

    public function index()
    {
        $pedidos = Pedido::latest()->get();
        return view('pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        return view('pedidos.show', compact('pedido'));
    }

    public function finalizar(PedidoRequest $request, CartService $cart, CepService $cep)
    {
        // 1) Pega itens do carrinho (sempre array)
        $itens = session('carrinho', []);

        // 2) Calcula subtotal
        $subtotal = collect($itens)
            ->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']);

        // 3) Aplica cupom
        $cart->aplicarCupom($subtotal, $request->cupom);

        // 4) Calcula frete
        $frete = $cart->calcularFrete($subtotal);

        // 5) Busca endereço
        $dadosCep = $cep->buscar($request->cep);
        $endereco = "{$dadosCep['logradouro']}, {$dadosCep['bairro']}, {$dadosCep['localidade']}";

        // 6) Persiste em transação
        DB::transaction(function() use ($itens, $subtotal, $frete, $request, $endereco) {
            $pedido = Pedido::create([
                'itens'    => $itens,
                'subtotal' => $subtotal,
                'frete'    => $frete,
                'total'    => $subtotal + $frete,
                'cep'      => $request->cep,
                'endereco' => $endereco,
                'status'   => 'pendente',
            ]);

            Mail::to($request->email)
                ->queue(new \App\Mail\PedidoConfirmado($pedido));
        });

        // 7) Limpa carrinho
        session()->forget('carrinho');

        // 8) Redirect consistente
        return redirect('/produtos');
    }
}
