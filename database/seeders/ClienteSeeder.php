<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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
        $user = User::all()->first();    	

		if($user){			
			Cliente::firstOrCreate([        	
	        	'user_id' => $user->id,
	        	'nome' => 'Isabelle',
				'cpf' => '111111111111',
				'data_nascimento' => '1994-09-15'
	        ]);	

	        Cliente::firstOrCreate([        	
	        	'user_id' => $user->id,
	        	'nome' => 'Daniel',
				'cpf' => '22222222222',
				'data_nascimento' => '1997-12-26'
	        ]);

	        Cliente::firstOrCreate([        	
	        	'user_id' => $user->id,
	        	'nome' => 'Bianca',
				'cpf' => '33333333333',
				'data_nascimento' => '1996-12-10'
	        ]);			
		}
    }
}
