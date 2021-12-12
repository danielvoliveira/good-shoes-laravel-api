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
		$user = User::all()->first();    	

		if($user){
			for($x=0; $x < 10; $x++){
				Produto::firstOrCreate([
		        	'nome' => 'Sapato numero '. $x,
		        	'descricao' => 'O sapato mais elegante ' . $x,
		        	'cor' => 'Preto',
		        	'user_id' => $user->id
		        ]);
			}			
		}
    }
}
