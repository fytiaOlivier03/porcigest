@extends('layouts.app')
@section('titre', 'Connexion')
@section('contenu')
<article style="max-width:420px;margin:3rem auto">
    <h2>Connexion</h2>
    <form method="post" action="{{ route('login.attempt') }}">
        @csrf
        <label>E-mail <input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label>Mot de passe <input type="password" name="password" required></label>
        <label><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
        <button>Se connecter</button>
    </form>
</article>
@endsection
