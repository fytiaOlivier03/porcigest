<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistributionRequest;
use App\Models\Campagne;
use App\Models\DistributionAliment;
use App\Models\Intrant;
use App\Services\StockService;
use DomainException;
use Illuminate\Http\Request;

class DistributionAlimentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', DistributionAliment::class);

        $distributions = DistributionAliment::with(['campagne', 'intrant', 'user'])
            ->when($request->filled('campagne_id'), fn ($q) => $q->where('campagne_id', $request->integer('campagne_id')))
            ->latest('date_distribution')->latest('id')
            ->paginate(10)->withQueryString();

        return view('distributions.index', [
            'distributions' => $distributions,
            'campagnes' => Campagne::where('statut', 'en_cours')->orderBy('nom')->get(),
            'aliments' => Intrant::where('categorie', 'aliment')->orderBy('nom')->get(),
        ]);
    }

    public function store(StoreDistributionRequest $request, StockService $stock)
    {
        try {
            $stock->distribuerAliment($request->validated(), $request->user());
        } catch (DomainException $e) {   // inclut StockInsuffisantException
            return back()->withErrors(['quantite' => $e->getMessage()])->withInput();
        }

        return back()->with('succes', 'Distribution enregistrée, stock mis à jour.');
    }
}
