<?php

namespace Database\Factories;

use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pedido>
 */
class PedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Pedido::class;

    public function definition()
    {
        // Primeiro, crie de 1 a 3 produtos para compor o pedido
        $produtos = Produto::factory()->count($this->faker->numberBetween(1, 3))->create();

        // Monte os itens com quantidade e preço unitário
        $itens = $produtos->map(function($p) {
            $qtd = $this->faker->numberBetween(1, 5);
            return [
                'produto_id'     => $p->id,
                'variacao'       => null,
                'quantidade'     => $qtd,
                'preco_unitario' => $p->preco,
            ];
        })->toArray();

        // Calcule subtotal e frete simples (ex.: frete grátis para subtotal >200)
        $subtotal = collect($itens)->sum(fn($i) => $i['quantidade'] * $i['preco_unitario']);
        $frete    = $subtotal > 200 ? 0.00 : 20.00;

        return [
            'itens'    => $itens,
            'subtotal' => $subtotal,
            'frete'    => $frete,
            'total'    => $subtotal + $frete,
            'cep'      => $this->faker->regexify('\d{8}'),
            'endereco' => $this->faker->streetAddress() . ', ' . $this->faker->city(),
            'status'   => $this->faker->randomElement(['pendente', 'pago', 'cancelado']),
        ];
    }
}
