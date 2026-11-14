<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $ok = Auth::attempt(
            ['email' => $request->email, 'password' => $request->password, 'actif' => true],
            $request->boolean('remember')
        );

        if (! $ok) {
            Audit::warning('Échec de connexion', ['email' => $request->email, 'ip' => $request->ip()]);

            return back()->withErrors(['email' => 'Identifiants invalides ou compte désactivé.'])->onlyInput('email');
        }

        $request->session()->regenerate();   // protection contre la fixation de session

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
