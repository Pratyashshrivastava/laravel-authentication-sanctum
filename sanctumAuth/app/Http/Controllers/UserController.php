<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
}
