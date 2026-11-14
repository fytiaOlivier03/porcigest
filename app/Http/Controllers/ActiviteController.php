<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActiviteRequest;
use App\Models\Activite;
use App\Models\Campagne;
use App\Models\User;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    /** Recherche + filtres + tri + pagination. */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Activite::class);

        $tri = in_array($request->tri, ['date_activite', 'cout', 'type'], true) ? $request->tri : 'date_activite';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        $activites = Activite::with(['campagne.unite', 'user'])
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%'.$request->q.'%'))
            ->when($request->filled('campagne_id'), fn ($q) => $q->where('campagne_id', $request->integer('campagne_id')))
            ->when($request->filled('unite_id'), fn ($q) => $q->whereHas('campagne', fn ($c) => $c->where('unite_id', $request->integer('unite_id'))))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('date_activite', '>=', $request->du))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('date_activite', '<=', $request->au))
            ->orderBy($tri, $dir)->orderByDesc('id')
            ->paginate(10)->withQueryString();

        return view('activites.index', [
            'activites' => $activites,
            'campagnes' => Campagne::orderBy('nom')->get(),
            'utilisateurs' => User::where('exploitation_id', $request->user()->exploitation_id)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreActiviteRequest $request)
    {
        Activite::create($request->validated() + ['user_id' => $request->user()->id, 'cout' => $request->cout ?? 0]);

        return back()->with('succes', 'Activité enregistrée.');
    }
}
