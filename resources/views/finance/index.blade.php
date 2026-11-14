@extends('layouts.app')
@section('titre', 'Finances')
@section('contenu')
@php $ar = fn ($v) => number_format($v, 0, ',', ' ').' Ar'; @endphp
<h1>Finances</h1>
<div class="grid">
    <article>Dépenses<h3>{{ $ar($totaux['depenses']) }}</h3></article>
    <article>Recettes<h3>{{ $ar($totaux['recettes']) }}</h3></article>
    <article>Marge simplifiée (recettes − dépenses)<h3 class="{{ $totaux['marge'] < 0 ? 'crit' : '' }}">{{ $ar($totaux['marge']) }}</h3></article>
</div>

<div class="grid">
<div>
    <h3>Dépenses</h3>
    <table>@foreach ($depenses as $d) <tr><td>{{ $d->date_depense->format('d/m/Y') }}</td><td>{{ $d->categorie }}</td><td>{{ $d->libelle }}</td><td>{{ $ar($d->montant) }}</td></tr> @endforeach</table>
    {{ $depenses->links() }}
    <form method="post" action="{{ route('finance.depenses.store') }}">@csrf
        <select name="campagne_id"><option value="">— hors campagne —</option>@foreach ($campagnes as $c) <option value="{{ $c->id }}">{{ $c->nom }}</option> @endforeach</select>
        <select name="categorie">@foreach (App\Models\Depense::CATEGORIES as $c) <option>{{ $c }}</option> @endforeach</select>
        <input name="libelle" placeholder="Libellé" required>
        <input type="number" step="0.01" name="montant" placeholder="Montant (Ar)" required>
        <input type="date" name="date_depense" value="{{ now()->toDateString() }}" required>
        <button>Ajouter une dépense</button>
    </form>
</div>
<div>
    <h3>Recettes</h3>
    <table>@foreach ($recettes as $r) <tr><td>{{ $r->date_recette->format('d/m/Y') }}</td><td>{{ $r->categorie }}</td><td>{{ $r->libelle }}</td><td>{{ $ar($r->montant) }}</td></tr> @endforeach</table>
    {{ $recettes->links() }}
    <form method="post" action="{{ route('finance.recettes.store') }}">@csrf
        <select name="campagne_id"><option value="">— hors campagne —</option>@foreach ($campagnes as $c) <option value="{{ $c->id }}">{{ $c->nom }}</option> @endforeach</select>
        <select name="categorie">@foreach (App\Models\Recette::CATEGORIES as $c) <option>{{ $c }}</option> @endforeach</select>
        <input name="libelle" placeholder="Libellé" required>
        <input type="number" step="0.01" name="montant" placeholder="Montant (Ar)" required>
        <input type="date" name="date_recette" value="{{ now()->toDateString() }}" required>
        <button>Ajouter une recette</button>
    </form>
</div>
</div>
@endsection
