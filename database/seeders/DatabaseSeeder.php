<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\VendedorSeeder;
use Database\Seeders\ProdutoSeeder;
use Database\Seeders\LoteSeeder;
use Database\Seeders\ClienteSeeder;
use Database\Seeders\CorSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeed::class,
        	VendedorSeeder::class,
            ClienteSeeder::class,
            CorSeeder::class,
        	ProdutoSeeder::class,
			LoteSeeder::class,
            //PedidoSeeder::class
        ]);
    }
}
