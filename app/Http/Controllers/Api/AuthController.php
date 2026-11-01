<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $datos = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $datos['username'])->first();

        if (!$user || !Hash::check($datos['password'], $user->password)) {
            return response()->json(['message' => 'El usuario o la contraseña son incorrectos.'], 422);
        }

        $token = Str::random(60);
        $user->forceFill(['api_token' => hash('sha256', $token)])->save();

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->forceFill(['api_token' => null])->save();

        return response()->json(['ok' => true]);
    }
}
