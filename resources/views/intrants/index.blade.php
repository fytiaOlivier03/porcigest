@extends('layouts.app')
@section('titre', 'Intrants et stocks')
@section('contenu')
<h1>Intrants et stocks</h1>
<form method="get" class="grid-form">
    <label>Recherche <input name="q" value="{{ request('q') }}"></label>
    <label>Catégorie <select name="categorie"><option value="">Toutes</option>
        @foreach (App\Models\Intrant::CATEGORIES as $c) <option @selected(request('categorie') === $c)>{{ $c }}</option> @endforeach</select></label>
    <label><input type="checkbox" name="critique" value="1" @checked(request('critique'))> Stocks critiques seulement</label>
    <button>Filtrer</button>
</form>
<table>
    <thead><tr><th>Intrant</th><th>Catégorie</th><th>Disponible</th><th>Seuil</th><th>Coût unitaire</th><th></th></tr></thead>
    @foreach ($intrants as $i)
        <tr>
            <td><a href="{{ route('intrants.show', $i) }}">{{ $i->nom }}</a></td><td>{{ $i->categorie }}</td>
            <td class="{{ $i->isCritique() ? 'crit' : '' }}">{{ $i->quantite_disponible }} {{ $i->unite_mesure }}</td>
            <td>{{ $i->seuil_minimum }}</td><td>{{ number_format($i->cout_unitaire, 0, ',', ' ') }} Ar</td>
            <td>@if ($i->isCritique()) <span class="crit"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.3 3.9 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3l-8.1-14.1a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 4h.01"/></svg> critique</span> @endif</td>
        </tr>
    @endforeach
</table>
{{ $intrants->links() }}

@can('create', App\Models\Intrant::class)
<h3>Nouvel intrant</h3>
<form method="post" action="{{ route('intrants.store') }}" class="grid-form">@csrf
    <label>Nom <input name="nom" value="{{ old('nom') }}" required></label>
    <label>Catégorie <select name="categorie">@foreach (App\Models\Intrant::CATEGORIES as $c) <option>{{ $c }}</option> @endforeach</select></label>
    <label>Unité de mesure <input name="unite_mesure" value="{{ old('unite_mesure', 'kg') }}" required></label>
    <label>Seuil minimum <input type="number" step="0.01" min="0" name="seuil_minimum" value="{{ old('seuil_minimum', 0) }}" required></label>
    <label>Coût unitaire (Ar) <input type="number" step="0.01" min="0" name="cout_unitaire" value="{{ old('cout_unitaire', 0) }}" required></label>
    <label>Stock initial <input type="number" step="0.01" min="0" name="quantite_initiale" value="{{ old('quantite_initiale') }}"></label>
    <button>Créer</button>
</form>
@endcan
@endsection
