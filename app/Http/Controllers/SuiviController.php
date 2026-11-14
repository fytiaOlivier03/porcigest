<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMortaliteRequest;
use App\Http\Requests\StorePeseeRequest;
use App\Http\Requests\StoreProductionRequest;
use App\Models\Campagne;
use App\Models\Mortalite;
use App\Models\Pesee;
use App\Models\Production;
use App\Services\ZootechnieService;
use App\Support\Audit;

/** Saisies de suivi d'une campagne : pesées, mortalités, productions. */
class SuiviController extends Controller
{
    public function pesee(StorePeseeRequest $request, Campagne $campagne)
    {
        $this->authorize('view', $campagne);
        Pesee::create($request->validated() + ['campagne_id' => $campagne->id, 'user_id' => $request->user()->id]);

        return back()->with('succes', 'Pesée enregistrée.');
    }

    public function mortalite(StoreMortaliteRequest $request, Campagne $campagne, ZootechnieService $zoo)
    {
        $this->authorize('view', $campagne);

        $actuel = $zoo->indicateurs($campagne)['effectif_actuel'];
        if ($request->integer('nombre') > $actuel) {
            return back()->withErrors(['nombre' => "Le nombre de morts dépasse l'effectif actuel ({$actuel})."])->withInput();
        }

        Mortalite::create($request->validated() + ['campagne_id' => $campagne->id, 'user_id' => $request->user()->id]);

        $ind = $zoo->indicateurs($campagne);
        if ($ind['alerte_mortalite']) {
            Audit::warning('Mortalité élevée', ['campagne' => $campagne->nom, 'taux' => $ind['taux_mortalite']]);
        }

        return back()->with('succes', 'Mortalité enregistrée.');
    }

    public function production(StoreProductionRequest $request, Campagne $campagne)
    {
        $this->authorize('view', $campagne);
        Production::create($request->validated() + ['campagne_id' => $campagne->id]);

        return back()->with('succes', 'Production enregistrée.');
    }
}
