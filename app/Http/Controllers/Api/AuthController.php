<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request){

        $valiteUser = Validator::make(
            $request->all(),
            [
                'name' => 'string | required',
                'email' => 'required | email | unique:users,email',
                'password' => 'required',

            ]
        );

        if($valiteUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $valiteUser->errors()->all(),
            ], 401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Register Successfully!',
            'user' => $user,
        ], 200);
    }

    public function login(Request $request){
        
        $valiteUser = Validator::make(
            $request->all(),
            [
                'email' => 'required | email ',
                'password' => 'required',

            ]
        );

        if($valiteUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Fail',
                'errors' => $valiteUser->errors()->all(),
            ], 404);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            
            $auth_user = Auth::user();

            return response()->json([
                'status' => true,
                'message' => 'User logged in Successfully!',
                'token' => $auth_user->createToken('API Token')->plainTextToken,
                'token_type' => 'bearer',
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => "Email & Password Don't Match",
            ], 401);
        }

    }
    public function logout(Request $request){
        
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'You logged Out Successfully!',
        ], 200);

    }
}
