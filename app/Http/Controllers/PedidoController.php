<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinalizarPedidoRequest;
use App\Http\Requests\PedidoRequest;
use App\Mail\PedidoConfirmado;
use App\Models\Pedido;
use App\Services\CartService;
use App\Services\CepService;
use Illuminate\Http\Request;
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

    public function create(CartService $cart)
    {
        $itens    = session('carrinho', []);
        $subtotal = collect($itens)
            ->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']);
        $frete = $cart->calcularFrete($subtotal);
        $total = $subtotal + $frete;
        return view('pedidos.create', compact('itens','subtotal','frete','total'));
    }

    public function finalizar(FinalizarPedidoRequest $request, CartService $cart, CepService $cep)
    {
        $itens = session('carrinho', []);

        $subtotal = collect($itens)
            ->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']);
        $cart->aplicarCupom($subtotal, $request->cupom);

        $frete = $cart->calcularFrete($subtotal);

        $dadosCep = $cep->buscar($request->cep);
        $endereco = "{$dadosCep['logradouro']}, {$dadosCep['bairro']}, {$dadosCep['localidade']}";

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

            foreach ($itens as $item) {
                \App\Models\Estoque::where('produto_id', $item['produto_id'])
                    ->where('variacao', $item['variacao'])
                    ->decrement('quantidade', $item['quantidade']);
            }

            Mail::to($request->email)
                ->send(new PedidoConfirmado($pedido)); 

            // Mail::to($request->email)
            // ->queue(new \App\Mail\PedidoConfirmado($pedido));
        });

        session()->forget('carrinho');
        return redirect('/pedidos');
    }
}
