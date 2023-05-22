<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;


class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => \App\Models\User::inRandomOrder()->first()->id,
            "nome" => $this->faker->word,
            "descricao" => $this->faker->text(200),
            "cor_id" => \App\Models\Cor::inRandomOrder()->first()->id,
            "imagem" => $this->faker->imageUrl($width = 640, $height = 480)
        ];
    }
}
