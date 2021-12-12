<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PassportAuthController extends Controller
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
    	
        $validation = Validator::make($request->all(), [
            'name' => 'required|min:4',//minimo 4 caracteres
            'email' => 'required|email|unique:users,email',//e-mail deve ser único
            'password' => 'required|min:8',//minimo 8 caracteres
        ]);

        if(!$validation->fails()){
		    $user = User::create([
		        'name' => $request->name,
		        'email' => $request->email,
		        'password' => bcrypt($request->password)
		    ]);
		   
		    $token = $user->createToken('LaravelAuthApp')->accessToken;

		    return response()->json(['token' => $token], 200); //retornamos um token de 200 caracteres

        } else{

        	return response()->json([
                    'action' => 'registration',
                    'status' => 422,
                    'msg' => 'fail',
                    'errors' => $validation->errors(),
                ], 422);
        }
 
        
    }
 
    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }   
}