<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendedor;

class VendedorSeeder extends Seeder
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
			Vendedor::firstOrCreate([        	
	        	'user_id' => $user->id,
	        	'nome' => 'Roberto',
				'data_contratacao' => '2020-09-15'
	        ]);	

	        Vendedor::firstOrCreate([        	
	        	'user_id' => $user->id,
	        	'nome' => 'Daniel',
				'data_contratacao' => '2020-12-26'
	        ]);

	        Vendedor::firstOrCreate([        	
	        	'user_id' => $user->id,
	        	'nome' => 'Bianca',
				'data_contratacao' => '2020-12-10'
	        ]);			
		}
    }
}

