@extends('layouts.app')
@section('titre', 'Campagnes')
@section('contenu')
<h1>Campagnes (lots de porcs)</h1>

<form method="get" class="grid-form">
    <label>Recherche <input name="q" value="{{ request('q') }}" placeholder="nom du lot"></label>
    <label>Statut <select name="statut"><option value="">Tous</option>
        @foreach (App\Models\Campagne::STATUTS as $s) <option value="{{ $s }}" @selected(request('statut') === $s)>{{ $s }}</option> @endforeach</select></label>
    <label>Unité <select name="unite_id"><option value="">Toutes</option>
        @foreach ($unites as $u) <option value="{{ $u->id }}" @selected(request('unite_id') == $u->id)>{{ $u->code }}</option> @endforeach</select></label>
    <label>Tri <select name="tri">
        <option value="date_debut" @selected(request('tri') === 'date_debut')>Date de début</option>
        <option value="nom" @selected(request('tri') === 'nom')>Nom</option>
        <option value="statut" @selected(request('tri') === 'statut')>Statut</option></select></label>
    <label>Ordre <select name="dir"><option value="desc">Décroissant</option><option value="asc" @selected(request('dir') === 'asc')>Croissant</option></select></label>
    <button>Filtrer</button>
</form>

<table>
    <thead><tr><th>Nom</th><th>Unité</th><th>Début</th><th>Fin prévue</th><th>Effectif</th><th>Statut</th></tr></thead>
    @forelse ($campagnes as $c)
        <tr>
            <td><a href="{{ route('campagnes.show', $c) }}">{{ $c->nom }}</a></td>
            <td>{{ $c->unite->code }}</td><td>{{ $c->date_debut->format('d/m/Y') }}</td>
            <td>{{ $c->date_fin_prevue->format('d/m/Y') }}</td><td>{{ $c->effectif_initial }}</td><td>{{ $c->statut }}</td>
        </tr>
    @empty
        <tr><td colspan="6">Aucune campagne.</td></tr>
    @endforelse
</table>
{{ $campagnes->links() }}

@can('create', App\Models\Campagne::class)
<h3>Nouvelle campagne</h3>
<form method="post" action="{{ route('campagnes.store') }}" class="grid-form">
    @csrf
    <label>Nom <input name="nom" value="{{ old('nom') }}" required></label>
    <label>Production <input name="production" value="{{ old('production', 'Porcs charcutiers') }}" required></label>
    <label>Unité <select name="unite_id">@foreach ($unites as $u) <option value="{{ $u->id }}" @selected(old('unite_id') == $u->id)>{{ $u->code }} – {{ $u->nom }} ({{ $u->capacite }})</option> @endforeach</select></label>
    <label>Date de début <input type="date" name="date_debut" value="{{ old('date_debut', now()->toDateString()) }}" required></label>
    <label>Fin prévue <input type="date" name="date_fin_prevue" value="{{ old('date_fin_prevue', now()->addDays(120)->toDateString()) }}" required></label>
    <label>Effectif initial <input type="number" name="effectif_initial" min="1" value="{{ old('effectif_initial') }}" required></label>
    <label>Statut <select name="statut"><option value="en_cours">en_cours</option><option value="planifiee">planifiee</option></select></label>
    <button>Créer</button>
</form>
@endcan
@endsection
