<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function status(Request $r)
    {
        $p = Pedido::find($r->id) ?? abort(404);
        $r->status === 'cancelado' ? $p->delete() : $p->update(['status' => $r->status]);

        return response()->json([], 200);
    }
}
