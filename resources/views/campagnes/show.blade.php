@extends('layouts.app')
@section('titre', $campagne->nom)
@section('contenu')
@php $ar = fn ($v) => number_format($v, 0, ',', ' ').' Ar'; @endphp
<h1>{{ $campagne->nom }} <small>({{ $campagne->statut }})</small></h1>
<p>{{ $campagne->production }} · Unité {{ $campagne->unite->code }} ({{ $campagne->unite->nom }}) ·
   {{ $campagne->date_debut->format('d/m/Y') }} → {{ $campagne->date_fin_prevue->format('d/m/Y') }}</p>

<div class="grid">
    <article><h3>{{ $ind['effectif_actuel'] }} / {{ $campagne->effectif_initial }}</h3>Effectif actuel</article>
    <article><h3 class="{{ $ind['alerte_mortalite'] ? 'crit' : '' }}">{{ $ind['taux_mortalite'] }} %</h3>Mortalité ({{ $ind['morts'] }})</article>
    <article><h3>{{ $ind['aliment_total'] }} kg</h3>Aliment consommé</article>
    <article><h3>{{ $ind['ic'] ?? 'n/d' }}</h3>Indice de consommation</article>
</div>
@if ($finance)
<div class="grid">
    <article>Dépenses : <strong>{{ $ar($finance['depenses']) }}</strong></article>
    <article>Recettes : <strong>{{ $ar($finance['recettes']) }}</strong></article>
    <article>Marge : <strong>{{ $ar($finance['marge']) }}</strong></article>
</div>
@endif

<div class="grid">
    @can('update', $campagne)
        @if ($campagne->statut === 'en_cours')
        <form method="post" action="{{ route('campagnes.terminer', $campagne) }}" onsubmit="return confirm('Terminer la campagne ?')">@csrf
            <button class="secondary">Terminer la campagne</button></form>
        @endif
    @endcan
    <form method="post" action="{{ route('campagnes.rapport', $campagne) }}">@csrf <button class="outline">Générer le rapport (queue)</button></form>
    @if ($rapportDispo) <a role="button" class="outline" href="{{ route('campagnes.rapport.telecharger', $campagne) }}">Télécharger le rapport</a> @endif
</div>

<h3>Distributions d'aliment récentes</h3>
<table>
    @forelse ($distributions as $d)
        <tr><td>{{ $d->date_distribution->format('d/m/Y') }}</td><td>{{ $d->intrant->nom }}</td><td>{{ $d->quantite }} {{ $d->intrant->unite_mesure }}</td><td>{{ $d->nombre_porcs }} porcs</td><td>{{ $d->user->name }}</td></tr>
    @empty <tr><td>Aucune distribution.</td></tr> @endforelse
</table>

<h3>Pesées</h3>
<table>
    @foreach ($pesees as $p) <tr><td>{{ $p->date_pesee->format('d/m/Y') }}</td><td>{{ $p->poids_moyen_kg }} kg (moyenne)</td><td>{{ $p->effectif_pese }} porcs pesés</td></tr> @endforeach
</table>
<form method="post" action="{{ route('campagnes.pesees.store', $campagne) }}" class="grid-form">@csrf
    <label>Date <input type="date" name="date_pesee" value="{{ now()->toDateString() }}" required></label>
    <label>Poids moyen (kg) <input type="number" step="0.01" name="poids_moyen_kg" required></label>
    <label>Effectif pesé <input type="number" name="effectif_pese" min="1" required></label>
    <button>Ajouter une pesée</button>
</form>

<h3>Mortalités</h3>
<table>
    @foreach ($mortalites as $m) <tr><td>{{ $m->date_mortalite->format('d/m/Y') }}</td><td>{{ $m->nombre }}</td><td>{{ $m->cause }}</td></tr> @endforeach
</table>
<form method="post" action="{{ route('campagnes.mortalites.store', $campagne) }}" class="grid-form">@csrf
    <label>Date <input type="date" name="date_mortalite" value="{{ now()->toDateString() }}" required></label>
    <label>Nombre <input type="number" name="nombre" min="1" required></label>
    <label>Cause <input name="cause"></label>
    <button>Déclarer</button>
</form>

<h3>Productions (sorties / ventes)</h3>
<table>
    @foreach ($productions as $p) <tr><td>{{ $p->date_production->format('d/m/Y') }}</td><td>{{ $p->nombre_porcs }} porcs</td><td>{{ $p->poids_total_kg }} kg</td></tr> @endforeach
</table>
@can('create', App\Models\Production::class)
<form method="post" action="{{ route('campagnes.productions.store', $campagne) }}" class="grid-form">@csrf
    <label>Date <input type="date" name="date_production" value="{{ now()->toDateString() }}" required></label>
    <label>Nombre de porcs <input type="number" name="nombre_porcs" min="1" required></label>
    <label>Poids total (kg) <input type="number" step="0.01" name="poids_total_kg" required></label>
    <button>Enregistrer</button>
</form>
@endcan

<h3>Activités récentes</h3>
<table>
    @foreach ($activites as $a) <tr><td>{{ $a->date_activite->format('d/m/Y') }}</td><td>{{ $a->type }}</td><td>{{ $a->description }}</td><td>{{ $a->user->name }}</td></tr> @endforeach
</table>
@endsection
