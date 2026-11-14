@extends('layouts.app')
@section('titre', 'Alimentation')
@section('contenu')
<h1>Distribution d'aliment</h1>
<p><small class="muted">Règle métier : le stock d'aliment ne peut jamais devenir négatif. Toute distribution crée un mouvement de stock et une dépense (transaction).</small></p>

<form method="post" action="{{ route('distributions.store') }}" class="grid-form">@csrf
    <label>Campagne <select name="campagne_id">@foreach ($campagnes as $c) <option value="{{ $c->id }}" @selected(old('campagne_id') == $c->id)>{{ $c->nom }}</option> @endforeach</select></label>
    <label>Aliment <select name="intrant_id">@foreach ($aliments as $a) <option value="{{ $a->id }}" @selected(old('intrant_id') == $a->id)>{{ $a->nom }} ({{ $a->quantite_disponible }} {{ $a->unite_mesure }})</option> @endforeach</select></label>
    <label>Quantité <input type="number" step="0.01" name="quantite" value="{{ old('quantite') }}" required></label>
    <label>Nombre de porcs <input type="number" name="nombre_porcs" min="1" value="{{ old('nombre_porcs') }}" required></label>
    <label>Date <input type="date" name="date_distribution" value="{{ old('date_distribution', now()->toDateString()) }}" required></label>
    <label>Observation <input name="observation" value="{{ old('observation') }}"></label>
    <button>Distribuer</button>
</form>

<h3>Historique</h3>
<table>
    <thead><tr><th>Date</th><th>Campagne</th><th>Aliment</th><th>Quantité</th><th>Porcs</th><th>Par</th></tr></thead>
    @foreach ($distributions as $d)
        <tr><td>{{ $d->date_distribution->format('d/m/Y') }}</td><td>{{ $d->campagne->nom }}</td><td>{{ $d->intrant->nom }}</td>
            <td>{{ $d->quantite }} {{ $d->intrant->unite_mesure }}</td><td>{{ $d->nombre_porcs }}</td><td>{{ $d->user->name }}</td></tr>
    @endforeach
</table>
{{ $distributions->links() }}
@endsection
