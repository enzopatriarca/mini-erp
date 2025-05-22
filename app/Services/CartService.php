<?php

namespace App\Services;

use App\Models\Cupom;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function adicionar(int $id, ?string $var): void
    {
        $cart = Session::get('carrinho', []);
        $cart[] = ['produto_id' => $id, 'variacao' => $var, 'quantidade' => 1];
        Session::put('carrinho', $cart);
    }

    public function calcularFrete(float $sub): float
    {
        return match (true) {
            $sub >= 200 => 0.0,
            $sub >= 52 => 15.0,
            default => 20.0,
        };
    }

    public function aplicarCupom(float &$sub, ?string $code): void
    {
        if (! $code) {
            return;
        }
        $c = Cupom::where('codigo', $code)->first();
        if ($c && now()->lte($c->validade) && $sub >= $c->minimo_subtotal) {
            $sub -= $c->desconto;
        }
    }
}
