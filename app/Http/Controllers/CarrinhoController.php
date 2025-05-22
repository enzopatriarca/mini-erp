<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CarrinhoController extends Controller
{
    public function adicionar(Request $r, CartService $cart)
    {
        $cart->adicionar($r->produto_id, $r->variacao);

        return back();
    }
}
