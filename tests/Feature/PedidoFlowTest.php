<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produto;
use App\Models\Cupom;
use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PedidoFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_fluxo_de_pedido_com_cupom_e_webhook()
    {
        // 1) Cria produto e cupom
        $produto = Produto::factory()->create(['preco' => 100]);
        $cupom   = Cupom::factory()->create([
            'desconto'        => 10,
            'minimo_subtotal' => 50,
            'validade'        => now()->addDay(),
        ]);

        // 2) Adiciona ao carrinho — só assertStatus 302
        $this->post(route('carrinho.adicionar'), [
            'produto_id' => $produto->id,
            'variacao'   => null,
        ])->assertStatus(302);

        // 3) Finaliza pedido — só assertStatus 302
        $this->post(route('pedido.finalizar'), [
            'cep'    => '01001000',
            'email'  => 'teste@ex.com',
            'cupom'  => $cupom->codigo,
        ])->assertStatus(302);

        // 4) Verifica que o pedido existe e subtotal foi aplicado
        $pedido = Pedido::first();
        // dd($pedido);
        $this->assertNotNull($pedido, 'O pedido não foi criado no banco.');
        $this->assertEquals(90, $pedido->subtotal);

        // 5) Dispara webhook e espera 200
        $this->postJson(route('webhook.status'), [
            'id'     => $pedido->id,
            'status' => 'pago',
        ])->assertOk();

        // 6) Confirma status no banco
        $this->assertDatabaseHas('pedidos', [
            'id'     => $pedido->id,
            'status' => 'pago',
        ]);
    }

}
