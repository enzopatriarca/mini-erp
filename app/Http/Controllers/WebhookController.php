<?php
namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Estoque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function status(Request $r)
    {
        Log::info('Webhook recebido:', $r->all());

        $pedido     = Pedido::findOrFail($r->id);
        $novoStatus = strtolower($r->status);

        DB::transaction(function() use ($pedido, $novoStatus, $r) {
            if ($novoStatus === 'cancelado') {
                foreach ($pedido->itens as $item) {
                    $variacao = $item['variacao'] ?: 'default';
                    Estoque::where('produto_id', $item['produto_id'])
                        ->where('variacao',    $variacao)
                        ->increment('quantidade', $item['quantidade']);
                }
                $pedido->delete();
            }
            elseif ($novoStatus === 'aprovado' && $pedido->status !== 'aprovado') {
                foreach ($pedido->itens as $item) {
                    $variacao = $item['variacao'] ?: 'default';
                    Estoque::where('produto_id', $item['produto_id'])
                        ->where('variacao',    $variacao)
                        ->decrement('quantidade', $item['quantidade']);
                }
                $pedido->update(['status' => $r->status]);
            }
            else {
                $pedido->update(['status' => $r->status]);
            }
        });

        return response()->json([], 200);
    }

}
