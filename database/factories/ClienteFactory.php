<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Cliente;
use Faker\Generator as Faker;


class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => '1',
            "nome" => $this->faker->name,
            "cpf" => $this->faker->unique()->numerify('###########'),
            "data_nascimento" => $this->faker->date($format = 'Y-m-d', $max = 'now')
        ];
    }
}
