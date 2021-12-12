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
	        	'data_fabricacao' => '1994-09-15',
				'quantidade_fabricada' => 20,
				'quantidade_disponivel' => 20,
				'valor_unitario' => 50.45,
	        ]);			
		}
    }
}
