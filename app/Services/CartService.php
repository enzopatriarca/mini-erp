<?php

namespace App\Services;

use App\Models\Cupom;
use App\Models\Produto;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function adicionar(int $produtoId, ?string $variacao = null): void
    {
        $carrinho = Session::get('carrinho', []);

        $produto = Produto::findOrFail($produtoId);

        // Tenta encontrar item igual no carrinho
        foreach ($carrinho as &$item) {
            if ($item['produto_id'] === $produto->id && $item['variacao'] === $variacao) {
                $item['quantidade']++;
                Session::put('carrinho', $carrinho);
                return;
            }
        }

        // Se não existir, adiciona novo
        $carrinho[] = [
            'produto_id'     => $produto->id,
            'variacao'       => $variacao,
            'quantidade'     => 1,
            'preco_unitario' => $produto->preco,
        ];

        Session::put('carrinho', $carrinho);
    }

    public function calcularFrete(float $subtotal): float
    {
        return match (true) {
            $subtotal >= 200 => 0.0,
            $subtotal >= 52  => 15.0,
            default          => 20.0,
        };
    }


    public function aplicarCupom(float &$subtotal, ?string $codigo): void
    {
        if (!$codigo) {
            return;
        }

        $cupom = \App\Models\Cupom::where('codigo', $codigo)->first();
        if ($cupom && now()->lte($cupom->validade) && $subtotal >= $cupom->minimo_subtotal) {
            $subtotal -= $cupom->desconto;
        }
    }
}
