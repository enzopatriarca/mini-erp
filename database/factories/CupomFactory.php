<?php

namespace Database\Factories;

use App\Models\Cupom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cupom>
 */
class CupomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Cupom::class;

    public function definition()
    {
        return [
            'codigo'          => strtoupper($this->faker->bothify('CUPOM-####')),
            'desconto'        => $this->faker->randomFloat(2, 5, 50),
            'minimo_subtotal' => $this->faker->randomFloat(2, 0, 100),
            'validade'        => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
        ];
    }
}
