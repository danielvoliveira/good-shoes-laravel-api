<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Cliente;
use App\Lote;
use App\Vendedor;
use Faker\Generator as Faker;

class PedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        //Quantidade de Item Pedido
        $quantidade_item_pedido = random_int(1, 10);

        for($x=0; $x <= $quantidade_item_pedido; $x++){
            //Quantidade do Lote
            $quantidade_lote = 1; //$quantidade_item_pedido = random_int(1, 10);

            //PAREI AQUI
        }

        return [
            "user_id" => \App\Models\User::inRandomOrder()->first()->id,
            "cliente_id" => \App\Models\Produto::inRandomOrder()->first()->id,
            "vendedor_id" => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            "data_pedido" => $quantidade_fabricada,
            "quantidade_disponivel" => $quantidade_fabricada,
            "valor_unitario" => $this->faker->randomFloat(2, 0.1, 300)
        ];
    }
}
