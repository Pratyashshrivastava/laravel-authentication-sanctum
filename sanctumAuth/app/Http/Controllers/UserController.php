<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Register function Logic
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required',
            'tc' => 'required',
            'password' => 'required|confirmed'
        ]);

        // $validatedData['password'] = bcrypt($request->password);

        // $user = User::create($validatedData);

        // $accessToken = $user->createToken('authToken')->accessToken;

        // return response(['user' => $user, 'access_token' => $accessToken]);

        if(User::where('email', $request->email)->first()) {
            return response([
                'message' => 'Email already exists',
                'status' => 'failed'
            ]);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'tc' => json_decode($request->tc),
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken($request->email)->plainTextToken;
        return response([
            'token' => $token, 
            'message' => 'User created successfully',
            'status' => 'success',
            // 'user' => $user
        ]);
    }

    // Login function Logic
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response([
                'message' => 'User not found',
                'status' => 'failed'
            ]);
        }
        if(!Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Incorrect password',
                'status' => 'failed'
            ]);
        }
        $token = $user->createToken($request->email)->plainTextToken;
        return response([
            'token' => $token, 
            'message' => 'User logged in successfully',
            'status' => 'success',
            // 'user' => $user
        ]);
    }

    // Logout function Logic
    public function logout(){
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'User logged out successfully',
            'status' => 'success'
        ]);
    }

    public function loggedUser(){
        // auth()->user()->tokens()->delete();
        $loggedUser = auth()->user();
        return response([
            'user' => $loggedUser,
            'message' => 'logged user Data',
            'status' => 'success'
        ]);
    }
}
