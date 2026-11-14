@extends('layouts.app')
@section('titre', 'Unités de production')
@section('contenu')
<h1>Unités de production</h1>
<table>
    <thead><tr><th>Code</th><th>Nom</th><th>Type</th><th>Capacité</th><th>État</th><th>Campagnes</th><th></th></tr></thead>
    @foreach ($unites as $u)
        <tr>
            <td>{{ $u->code }}</td><td>{{ $u->nom }}</td><td>{{ $u->type }}</td><td>{{ $u->capacite }}</td>
            <td>{{ $u->etat }}</td><td>{{ $u->campagnes_count }}</td>
            <td>@can('delete', $u)
                <form method="post" action="{{ route('unites.destroy', $u) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')
                    <button class="secondary outline" style="padding:.1rem .5rem">Supprimer</button></form>
            @endcan</td>
        </tr>
    @endforeach
</table>
{{ $unites->links() }}

@can('create', App\Models\Unite::class)
<h3>Nouvelle unité</h3>
<form method="post" action="{{ route('unites.store') }}" class="grid-form">
    @csrf
    <label>Code <input name="code" value="{{ old('code') }}" required></label>
    <label>Nom <input name="nom" value="{{ old('nom') }}" required></label>
    <label>Type
        <select name="type">@foreach (App\Models\Unite::TYPES as $t) <option>{{ $t }}</option> @endforeach</select></label>
    <label>Capacité <input type="number" name="capacite" min="1" value="{{ old('capacite') }}" required></label>
    <label>Localisation <input name="localisation" value="{{ old('localisation') }}"></label>
    <button>Créer</button>
</form>
@endcan
@endsection
