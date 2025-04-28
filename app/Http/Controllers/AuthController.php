<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>['required', 'string'],
            'email'=> ['required', 'email', 'unique:users'],
            'password'=>['required','min:6']
        ]);

        $user = User::create($data);
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);

    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ]);
    
        $user = User::where('email', $data['email'])->first();
    
        if (!$user) {
            return response()->json([
                'message' => 'Email not found.'
            ], 404);
        }
    
        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Wrong password.'
            ], 401);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Login successful!',
            'user' => $user,
            'token' => $token
        ], 200);
    }
    
}
