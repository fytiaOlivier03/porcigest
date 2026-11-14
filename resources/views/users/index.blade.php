@extends('layouts.app')
@section('titre', 'Utilisateurs')
@section('contenu')
<h1>Comptes de l'exploitation</h1>
<table>
    <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Actif</th><th></th></tr></thead>
    @foreach ($users as $u)
        <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->role }}</td><td>{{ $u->actif ? 'oui' : 'non' }}</td>
            <td><form method="post" action="{{ route('users.toggle', $u) }}">@csrf <button class="secondary outline" style="padding:.1rem .5rem">{{ $u->actif ? 'Désactiver' : 'Réactiver' }}</button></form></td></tr>
    @endforeach
</table>
<h3>Nouveau compte</h3>
<form method="post" action="{{ route('users.store') }}" class="grid-form">@csrf
    <label>Nom <input name="name" value="{{ old('name') }}" required></label>
    <label>E-mail <input type="email" name="email" value="{{ old('email') }}" required></label>
    <label>Rôle <select name="role">@foreach (App\Models\User::ROLES as $r) <option>{{ $r }}</option> @endforeach</select></label>
    <label>Mot de passe <input type="password" name="password" required></label>
    <button>Créer</button>
</form>
@endsection
