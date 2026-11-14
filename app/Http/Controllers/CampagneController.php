<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampagneRequest;
use App\Jobs\GenererRapportCampagne;
use App\Models\Campagne;
use App\Models\Unite;
use App\Services\CampagneService;
use App\Services\FinanceService;
use App\Services\ZootechnieService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CampagneController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Campagne::class);

        $tri = in_array($request->tri, ['nom', 'date_debut', 'statut'], true) ? $request->tri : 'date_debut';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        $campagnes = Campagne::with('unite')
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('unite_id'), fn ($q) => $q->where('unite_id', $request->integer('unite_id')))
            ->when($request->filled('q'), fn ($q) => $q->where('nom', 'like', '%'.$request->q.'%'))
            ->orderBy($tri, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('campagnes.index', ['campagnes' => $campagnes, 'unites' => Unite::orderBy('code')->get()]);
    }

    public function store(StoreCampagneRequest $request, CampagneService $service)
    {
        $campagne = $service->creer($request->validated());

        return redirect()->route('campagnes.show', $campagne)->with('succes', 'Campagne créée.');
    }

    public function show(Campagne $campagne, ZootechnieService $zoo, FinanceService $finance)
    {
        $this->authorize('view', $campagne);
        $campagne->load('unite');

        return view('campagnes.show', [
            'campagne' => $campagne,
            'ind' => $zoo->indicateurs($campagne),
            'finance' => Gate::allows('voir-finances') ? $finance->totaux($campagne->id) : null,
            'distributions' => $campagne->distributions()->with(['intrant', 'user'])->latest('date_distribution')->latest('id')->limit(10)->get(),
            'activites' => $campagne->activites()->with('user')->latest('date_activite')->latest('id')->limit(10)->get(),
            'pesees' => $campagne->pesees()->latest('date_pesee')->latest('id')->limit(10)->get(),
            'mortalites' => $campagne->mortalites()->latest('date_mortalite')->latest('id')->limit(10)->get(),
            'productions' => $campagne->productions()->latest('date_production')->latest('id')->limit(10)->get(),
            'rapportDispo' => Storage::exists("rapports/campagne-{$campagne->id}.txt"),
        ]);
    }

    public function terminer(Campagne $campagne, CampagneService $service)
    {
        $this->authorize('update', $campagne);

        try {
            $service->terminer($campagne);
        } catch (DomainException $e) {
            return back()->withErrors(['campagne' => $e->getMessage()]);
        }

        return back()->with('succes', 'Campagne terminée. Le rapport est en cours de génération.');
    }

    /** Génération du rapport en tâche différée (queue). */
    public function genererRapport(Campagne $campagne)
    {
        $this->authorize('view', $campagne);
        GenererRapportCampagne::dispatch($campagne->id);

        return back()->with('succes', 'Génération du rapport lancée (queue). Rechargez la page dans un instant.');
    }

    public function telechargerRapport(Campagne $campagne)
    {
        $this->authorize('view', $campagne);
        $chemin = "rapports/campagne-{$campagne->id}.txt";

        abort_unless(Storage::exists($chemin), 404);

        return Storage::download($chemin);
    }
}
