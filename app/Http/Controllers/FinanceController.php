<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepenseRequest;
use App\Http\Requests\StoreRecetteRequest;
use App\Models\Campagne;
use App\Models\Depense;
use App\Models\Recette;
use App\Services\FinanceService;

class FinanceController extends Controller
{
    public function index(FinanceService $finance)
    {
        $this->authorize('viewAny', Depense::class);

        return view('finance.index', [
            'totaux' => $finance->totaux(),
            'depenses' => Depense::with('campagne')->latest('date_depense')->latest('id')->paginate(8, ['*'], 'page_depenses'),
            'recettes' => Recette::with('campagne')->latest('date_recette')->latest('id')->paginate(8, ['*'], 'page_recettes'),
            'campagnes' => Campagne::orderBy('nom')->get(),
        ]);
    }

    public function storeDepense(StoreDepenseRequest $request)
    {
        Depense::create($request->validated() + ['user_id' => $request->user()->id]);

        return back()->with('succes', 'Dépense enregistrée.');
    }

    public function storeRecette(StoreRecetteRequest $request)
    {
        Recette::create($request->validated() + ['user_id' => $request->user()->id]);

        return back()->with('succes', 'Recette enregistrée.');
    }
}
