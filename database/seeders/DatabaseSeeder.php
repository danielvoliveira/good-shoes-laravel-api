<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ProdutoSeeder;
use Database\Seeders\LoteSeeder;

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
        	ProdutoSeeder::class,
			LoteSeeder::class,
        ]);
    }
}
