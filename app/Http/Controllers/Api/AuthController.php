<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! $user->actif || ! Hash::check($request->password, $user->password)) {
            Audit::warning('Échec de connexion API', ['email' => $request->email, 'ip' => $request->ip()]);

            return response()->json(['message' => 'Identifiants invalides.'], 401);
        }

        return response()->json(['token' => $user->createToken('api')->plainTextToken]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }
}
