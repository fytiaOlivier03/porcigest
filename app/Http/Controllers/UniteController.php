<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniteRequest;
use App\Models\Unite;

class UniteController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Unite::class);

        return view('unites.index', ['unites' => Unite::withCount('campagnes')->orderBy('code')->paginate(10)]);
    }

    public function store(StoreUniteRequest $request)
    {
        Unite::create($request->validated() + ['etat' => 'disponible']);

        return back()->with('succes', 'Unité de production créée.');
    }

    public function destroy(Unite $unite)
    {
        $this->authorize('delete', $unite);

        if ($unite->campagnes()->exists()) {
            return back()->withErrors(['unite' => 'Impossible de supprimer une unité qui possède des campagnes.']);
        }
        $unite->delete();

        return back()->with('succes', 'Unité supprimée.');
    }
}
