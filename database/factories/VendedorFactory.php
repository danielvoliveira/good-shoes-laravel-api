<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Vendedor;
use Faker\Generator as Faker;

class VendedorFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => '1',
            'nome' => $this->faker->name,
            'data_contratacao' => $this->faker->date($format = 'Y-m-d', $max = 'now')
        ];
    }
}
