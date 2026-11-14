<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('gerer-utilisateurs');

        return view('users.index', [
            'users' => User::where('exploitation_id', $request->user()->exploitation_id)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = new User($request->validated());
        $user->exploitation_id = $request->user()->exploitation_id;   // jamais issu du formulaire
        $user->save();

        return back()->with('succes', 'Compte créé.');
    }

    public function toggle(Request $request, User $user)
    {
        Gate::authorize('gerer-utilisateurs');
        // User n'a pas de scope global : isolation vérifiée explicitement.
        abort_unless($user->exploitation_id === $request->user()->exploitation_id, 404);

        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }
        $user->update(['actif' => ! $user->actif]);

        return back()->with('succes', 'Statut du compte modifié.');
    }
}
