<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CorFactory extends Factory
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
            "nome" => $this->faker->unique()->colorName,
            "hexadecimal" => $this->faker->unique()->hexcolor
        ];
    }
}
