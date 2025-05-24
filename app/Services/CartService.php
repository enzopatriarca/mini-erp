<?php

namespace App\Services;

use App\Models\Cupom;
use App\Models\Produto;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function adicionar(int $produtoId, ?string $variacao = null, int $quantidade = 1): void
    {
        $carrinho = Session::get('carrinho', []);
        $produto = Produto::findOrFail($produtoId);

        foreach ($carrinho as &$item) {
            if ($item['produto_id'] === $produto->id
                && $item['variacao'] === $variacao) {
                $item['quantidade'] += $quantidade;
                Session::put('carrinho', $carrinho);
                return;
            }
        }

        $carrinho[] = [
            'produto_id'     => $produto->id,
            'variacao'       => $variacao,
            'quantidade'     => $quantidade,
            'preco_unitario' => $produto->preco,
        ];

        Session::put('carrinho', $carrinho);
    }

    public function aplicarCupom(float &$subtotal, ?string $codigo): bool
    {
        if (!$codigo) {
            return false;
        }

        $cupom = Cupom::where('codigo', $codigo)
                    ->where('validade', '>=', now())
                    ->first();

        if (!$cupom || $subtotal < $cupom->minimo_subtotal) {
            return false;
        }

        $subtotal -= $cupom->desconto;
        return true;
    }

    public function calcularFrete(float $subtotal): float
    {
        return match (true) {
            $subtotal >= 200 => 0.0,
            $subtotal >= 52  => 15.0,
            default          => 20.0,
        };
    }

}
