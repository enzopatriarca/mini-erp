<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CarrinhoController extends Controller
{

    public function index(Request $request, CartService $cart)
    {
        // 1) Busca itens da sessão
        $itens = session('carrinho', []);

        // 2) Calcula subtotal
        $subtotal = collect($itens)
            ->sum(fn($i) => $i['quantidade'] * $i['preco_unitario']);

        // 3) Aplica cupom se vier via GET ?cupom=XXX
        $cart->aplicarCupom($subtotal, $request->cupom);

        // 4) Calcula frete
        $frete = $cart->calcularFrete($subtotal);

        // 5) Dispara a view do carrinho
        return view('carrinho.index', compact('itens','subtotal','frete','request'));
    }

    public function adicionar(Request $request, CartService $cart)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'variacao'   => 'nullable|string',
        ]);

        $cart->adicionar($request->produto_id, $request->variacao);

        // garante redirect para um lugar estável, ex: listagem de produtos
       return redirect('/produtos');
    }

}
