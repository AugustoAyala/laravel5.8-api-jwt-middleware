<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'password');
        
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        
        $user = User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);
        
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }
   
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()], 401);
        }
        try {    
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'No podemos encontrar una cuenta con estas credenciales. Asegurese de haber ingresado la informacion correcta.'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Error al iniciar sesion, por favor intente de nuevo.'], 500);
        }
        return response()->json(compact('token') , 200);
    }
 
    public function getAuthenticatedUser()
    {
        return response()->json(auth()->user());
    }
    
    
}
