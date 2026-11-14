@extends('layouts.app')
@section('titre', 'Tableau de bord')
@section('contenu')
@php $ar = fn ($v) => number_format($v, 0, ',', ' ').' Ar'; @endphp
<h1>Tableau de bord</h1>

<div class="grid">
    <article><h3>{{ $stats['campagnes_actives'] }}</h3>Campagnes actives</article>
    <article><h3>{{ $stats['nb_unites'] }}</h3>Unités de production</article>
    <article><h3>{{ $stats['effectif_total'] }}</h3>Porcs présents</article>
</div>

<div class="grid">
    <article><h3>{{ $stats['taux_mortalite_moyen'] }} %</h3>Taux de mortalité moyen</article>
    <article><h3>{{ $stats['ic_moyen'] ?? 'n/d' }}</h3>Indice de consommation moyen</article>
</div>

@can('voir-finances')
<div class="grid">
    <article><h3>{{ $ar($stats['depenses']) }}</h3>Dépenses</article>
    <article><h3>{{ $ar($stats['recettes']) }}</h3>Recettes</article>
    <article><h3 class="{{ $stats['marge'] < 0 ? 'crit' : '' }}">{{ $ar($stats['marge']) }}</h3>Marge simplifiée</article>
</div>
@endcan

@if ($stats['alertes']['stocks'] || $stats['alertes']['mortalite'])
<article class="ko">
    <strong><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.3 3.9 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3l-8.1-14.1a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 4h.01"/></svg> Alertes</strong>
    <ul>
        @foreach ($stats['alertes']['stocks'] as $n) <li>Stock critique : {{ $n }}</li> @endforeach
        @foreach ($stats['alertes']['mortalite'] as $n) <li>Mortalité élevée : {{ $n }}</li> @endforeach
    </ul>
</article>
@endif

<h3>État des stocks</h3>
<table>
    <thead><tr><th>Intrant</th><th>Disponible</th><th>Seuil</th><th></th></tr></thead>
    @foreach ($stats['stocks'] as $s)
        <tr>
            <td>{{ $s['nom'] }}</td>
            <td>{{ $s['quantite'] }} {{ $s['unite'] }}</td>
            <td>{{ $s['seuil'] }}</td>
            <td>@if ($s['critique']) <span class="crit">CRITIQUE</span> @else OK @endif</td>
        </tr>
    @endforeach
</table>

<h3>Activités récentes</h3>
<table>
    @forelse ($recentes as $a)
        <tr><td>{{ $a->date_activite->format('d/m/Y') }}</td><td>{{ $a->type }}</td><td>{{ $a->campagne->nom }}</td><td>{{ $a->user->name }}</td></tr>
    @empty
        <tr><td>Aucune activité.</td></tr>
    @endforelse
</table>
@endsection
