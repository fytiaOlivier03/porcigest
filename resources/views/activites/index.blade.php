@extends('layouts.app')
@section('titre', 'Activités')
@section('contenu')
<h1>Activités techniques</h1>
<form method="get" class="grid-form">
    <label>Recherche <input name="q" value="{{ request('q') }}"></label>
    <label>Campagne <select name="campagne_id"><option value="">Toutes</option>@foreach ($campagnes as $c) <option value="{{ $c->id }}" @selected(request('campagne_id') == $c->id)>{{ $c->nom }}</option> @endforeach</select></label>
    <label>Type <select name="type"><option value="">Tous</option>@foreach (App\Models\Activite::TYPES as $t) <option @selected(request('type') === $t)>{{ $t }}</option> @endforeach</select></label>
    <label>Utilisateur <select name="user_id"><option value="">Tous</option>@foreach ($utilisateurs as $u) <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option> @endforeach</select></label>
    <label>Du <input type="date" name="du" value="{{ request('du') }}"></label>
    <label>Au <input type="date" name="au" value="{{ request('au') }}"></label>
    <label>Tri <select name="tri"><option value="date_activite">Date</option><option value="cout" @selected(request('tri') === 'cout')>Coût</option><option value="type" @selected(request('tri') === 'type')>Type</option></select></label>
    <label>Ordre <select name="dir"><option value="desc">Décroissant</option><option value="asc" @selected(request('dir') === 'asc')>Croissant</option></select></label>
    <button>Filtrer</button>
</form>
<table>
    <thead><tr><th>Date</th><th>Type</th><th>Campagne</th><th>Description</th><th>Coût</th><th>Par</th></tr></thead>
    @forelse ($activites as $a)
        <tr><td>{{ $a->date_activite->format('d/m/Y') }}</td><td>{{ $a->type }}</td><td>{{ $a->campagne->nom }}</td>
            <td>{{ $a->description }}</td><td>{{ number_format($a->cout, 0, ',', ' ') }}</td><td>{{ $a->user->name }}</td></tr>
    @empty <tr><td colspan="6">Aucune activité.</td></tr> @endforelse
</table>
{{ $activites->links() }}

<h3>Nouvelle activité</h3>
<form method="post" action="{{ route('activites.store') }}" class="grid-form">@csrf
    <label>Campagne <select name="campagne_id">@foreach ($campagnes as $c) <option value="{{ $c->id }}">{{ $c->nom }}</option> @endforeach</select></label>
    <label>Type <select name="type">@foreach (App\Models\Activite::TYPES as $t) <option>{{ $t }}</option> @endforeach</select></label>
    <label>Date <input type="date" name="date_activite" value="{{ now()->toDateString() }}" required></label>
    <label>Coût (Ar) <input type="number" step="0.01" min="0" name="cout" value="0"></label>
    <label>Description <input name="description"></label>
    <button>Enregistrer</button>
</form>
@endsection
