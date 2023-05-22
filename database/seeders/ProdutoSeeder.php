<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\User;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Produto::factory()->times(count:20)->create();
    }
}
