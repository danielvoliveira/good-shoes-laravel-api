<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class LoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $quantidade_fabricada = $this->faker->unique()->numerify('###');

        return [
            "user_id" => \App\Models\User::inRandomOrder()->first()->id,
            "produto_id" => \App\Models\Produto::inRandomOrder()->first()->id,
            "data_fabricacao" => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            "quantidade_fabricada" => $quantidade_fabricada,
            "quantidade_disponivel" => $quantidade_fabricada,
            "valor_unitario" => $this->faker->randomFloat(2, 0.1, 300)
        ];
    }
}
