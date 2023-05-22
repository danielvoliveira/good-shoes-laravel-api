<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cor;

class CorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cor::factory()->times(count:10)->create();
    }
}
