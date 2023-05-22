<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
//use App\Models\User;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cliente::factory()->times(count:100)->create();
    }
}
