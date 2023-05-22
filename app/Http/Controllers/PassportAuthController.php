<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PassportAuthController extends Controller
{
    public function register(Request $request){
    	
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

		    return response()->json([
                'status' => true,
                'token' => $token,
                'message' => 'Register and login successfully'
            ], 200);

        }else{
        	return response()->json([
                    'status' => false,
                    'message' => $validation->errors(),
                    'sended_email' => $request->email
                ], 400);
        }
    }
 
    public function login(Request $request){
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if(auth()->attempt($data)){
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            
            return response()->json([
                'status' => true,
                'token' => $token,
                'message' => 'Login successfully'
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Login unauthorised'
            ], 401);
        }
    }

    public function logout(/*Request $request*/){
        
        $token = auth()->user()->token()->revoke();

        if($token){
            return response()->json([
                'status' => true,
                'message' => 'Logout successfully'
            ], 400);
        }else{
            return response()->json([
                'status' => true,
                'message' => 'Error when logging out'
            ], 400);
        }
    }
}