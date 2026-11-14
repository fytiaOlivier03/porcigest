@extends('layouts.app')
@section('titre', $intrant->nom)
@section('contenu')
<h1>{{ $intrant->nom }}</h1>
<p>Stock : <strong class="{{ $intrant->isCritique() ? 'crit' : '' }}">{{ $intrant->quantite_disponible }} {{ $intrant->unite_mesure }}</strong>
   · seuil minimum : {{ $intrant->seuil_minimum }} · catégorie : {{ $intrant->categorie }}</p>

@can('update', $intrant)
<form method="post" action="{{ route('intrants.mouvements.store', $intrant) }}" class="grid-form">@csrf
    <label>Type <select name="type"><option value="entree">Entrée</option><option value="ajustement">Ajustement (± quantité)</option></select></label>
    <label>Quantité <input type="number" step="0.01" name="quantite" required></label>
    <label>Date <input type="date" name="date_mouvement" value="{{ now()->toDateString() }}" required></label>
    <label>Motif <input name="motif"></label>
    <button>Enregistrer</button>
</form>
@endcan

<h3>Historique des mouvements (traçabilité)</h3>
<table>
    <thead><tr><th>Date</th><th>Type</th><th>Quantité</th><th>Avant → Après</th><th>Campagne</th><th>Par</th><th>Motif</th></tr></thead>
    @foreach ($mouvements as $m)
        <tr>
            <td>{{ $m->date_mouvement->format('d/m/Y') }}</td><td>{{ $m->type }}</td>
            <td>{{ $m->quantite > 0 ? '+' : '' }}{{ $m->quantite }}</td>
            <td>{{ $m->quantite_avant }} → {{ $m->quantite_apres }}</td>
            <td>{{ $m->campagne?->nom }}</td><td>{{ $m->user->name }}</td><td>{{ $m->motif }}</td>
        </tr>
    @endforeach
</table>
{{ $mouvements->links() }}
@endsection
