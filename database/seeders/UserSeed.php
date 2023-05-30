<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        	'id' => 1,
        	'name' => 'Daniel V A de Oliveira',
			'email' => 'danielvitol@hotmail.com',
			'password' => bcrypt('11111111')
        ]);

        //App\Models\User->createToken('LaravelAuthApp')->accessToken;
    }
}
