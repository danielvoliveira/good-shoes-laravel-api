<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lote;

class LoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::all()->first();    	

		if($user){			
			Lote::firstOrCreate([
	        	'produto_id' => 1,	        	
	        	'user_id' => $user->id,
	        	'data_fabricacao' => '2021-09-15',
				'quantidade_fabricada' => 20,
				'quantidade_disponivel' => 20,
				'valor_unitario' => 50.45
	        ]);

	        Lote::firstOrCreate([
	        	'produto_id' => 2,	        	
	        	'user_id' => $user->id,
	        	'data_fabricacao' => '2021-10-02',
				'quantidade_fabricada' => 30,
				'quantidade_disponivel' => 30,
				'valor_unitario' => 90.90
	        ]);		

	        Lote::firstOrCreate([
	        	'produto_id' => 3,	        	
	        	'user_id' => $user->id,
	        	'data_fabricacao' => '2021-05-12',
				'quantidade_fabricada' => 40,
				'quantidade_disponivel' => 40,
				'valor_unitario' => 32.95
	        ]);	
		}
    }
}
