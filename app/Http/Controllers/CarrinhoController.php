<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CarrinhoController extends Controller
{

    public function index(Request $request, CartService $cart)
    {
        $itens    = session('carrinho', []);
        $subtotal = collect($itens)
            ->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']);

        $cupom        = $request->query('cupom');
        $cupomError   = null;
        $cupomSuccess = false;
        $desconto     = 0.0;

        if ($cupom) {
            $antes = $subtotal;

            $aplicado = $cart->aplicarCupom($subtotal, $cupom);

            if ($aplicado) {
                $cupomSuccess = true;
                session()->flash('cupom_success', true);

                $desconto = $antes - $subtotal;
            } else {
                $msg = "Cupom “{$cupom}” inválido ou não atende ao mínimo.";
                $cupomError = $msg;
                session()->flash('cupom_error', $msg);
            }
        }

        $frete = $cart->calcularFrete($subtotal);
        $total = $subtotal + $frete;

        return view('carrinho.index', compact(
            'itens', 'subtotal', 'frete', 'total',
            'cupomError', 'cupomSuccess', 'desconto'
        ));
    }

    public function adicionar(Request $request, CartService $cart)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'variacao'   => 'nullable|string',
            'quantidade' => 'required|integer|min:1',
        ]);

        $produto = \App\Models\Produto::findOrFail($request->produto_id);
        $estoqueItem = $produto
            ->estoques()
            ->when($request->variacao, fn($q) => $q->where('variacao', $request->variacao))
            ->first();

        $disponivel = $estoqueItem->quantidade ?? 0;

        if ($request->quantidade > $disponivel) {
            return redirect()->back()
                             ->with('error', "Apenas {$disponivel} em estoque para essa variação.");
        }

        $cart->adicionar(
            $request->produto_id,
            $request->variacao,
            $request->quantidade
        );

        return redirect()->back()
                         ->with('success', 'Produto adicionado ao carrinho!');
    }

    public function atualizar(Request $request, CartService $cart)
    {
        $request->validate([
            'index'      => 'required|integer',
            'quantidade' => 'required|integer|min:1',
        ]);

        $c = session('carrinho', []);
        if (isset($c[$request->index])) {
            $c[$request->index]['quantidade'] = $request->quantidade;
            session(['carrinho' => $c]);
            return redirect()->route('carrinho.index')
                             ->with('success','Quantidade atualizada.');
        }

        return redirect()->route('carrinho.index')
                         ->with('error','Item não encontrado no carrinho.');
    }

    public function remover(Request $request)
    {
        $request->validate(['index'=>'required|integer']);
        $c = session('carrinho', []);
        if (isset($c[$request->index])) {
            array_splice($c, $request->index, 1);
            session(['carrinho'=>$c]);
            return redirect()->route('carrinho.index')
                             ->with('success','Item removido do carrinho.');
        }
        return redirect()->route('carrinho.index')
                         ->with('error','Item não encontrado.');
    }

}
