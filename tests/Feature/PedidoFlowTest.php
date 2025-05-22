<?php

namespace Tests\Feature;

use App\Models\Cupom;
use App\Models\Pedido;
use App\Models\Produto;
use Tests\TestCase;

class PedidoFlowTest extends TestCase
{
    public function test_fluxo_de_pedido_com_cupom_e_webhook()
    {
        $produto = Produto::factory()->create(['preco' => 100]);
        $cupom = Cupom::factory()->create([
            'desconto' => 10, 'minimo_subtotal' => 50, 'validade' => now()->addDay(),
        ]);

        // adicionar ao carrinho
        $response = $this->post('/carrinho/adicionar', [
            'produto_id' => $produto->id, 'variacao' => null,
        ]);
        $response->assertStatus(302);

        // finalizar pedido com cupom
        $response = $this->post('/pedido/finalizar', [
            'cep' => '01001000', 'email' => 'teste@ex.com', 'cupom' => $cupom->codigo,
        ]);
        $response->assertStatus(200)
            ->assertViewHas('pedido');

        $pedido = Pedido::first();
        $this->assertEquals(90, $pedido->subtotal);

        // webhook atualiza status
        $this->post('/webhook/status', ['id' => $pedido->id, 'status' => 'pago'])
            ->assertStatus(200);
        $this->assertDatabaseHas('pedidos', ['status' => 'pago']);
    }
}
