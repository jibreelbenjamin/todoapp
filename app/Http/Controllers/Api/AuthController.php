<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tokenResult = $user->createToken('auth_token');
        $token = $tokenResult->plainTextToken;

        $tokenResult->accessToken->expires_at = now()->addDays(7);
        $tokenResult->accessToken->save();

        return response()->json([
            'status' => true,
            'message' => 'OK uda daftar',
            'name' => $user->name,
            'email' => $user->email,
            'access_token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'GK BISA',
            ], 401);
        }

        $tokenResult = $user->createToken('auth_token');
        $token = $tokenResult->plainTextToken;

        $tokenResult->accessToken->expires_at = now()->addDays(7);
        $tokenResult->accessToken->save();

        return response()->json([
            'status' => true,
            'message' => 'OK login berhasil',
            'id_user' => $user->id_user,
            'name' => $user->name,
            'email' => $user->email,
            'access_token' => $token,
        ]);
    }

    public function profile(Request $request)
    {

        return response()->json([
            'status' => true,
            'message' => 'OK valid',
            'data' => $request->user(),
        ], 202);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'OK keluar']);
    }
}
