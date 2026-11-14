<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIntrantRequest;
use App\Models\Intrant;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntrantController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Intrant::class);

        $intrants = Intrant::query()
            ->when($request->filled('categorie'), fn ($q) => $q->where('categorie', $request->categorie))
            ->when($request->filled('q'), fn ($q) => $q->where('nom', 'like', '%'.$request->q.'%'))
            ->when($request->boolean('critique'), fn ($q) => $q->whereColumn('quantite_disponible', '<=', 'seuil_minimum'))
            ->orderBy('nom')
            ->paginate(10)
            ->withQueryString();

        return view('intrants.index', ['intrants' => $intrants]);
    }

    public function store(StoreIntrantRequest $request, StockService $stock)
    {
        DB::transaction(function () use ($request, $stock) {
            $intrant = Intrant::create($request->safe()->except('quantite_initiale'));

            if ((float) $request->quantite_initiale > 0) {
                $stock->entree($intrant->id, (float) $request->quantite_initiale, $request->user(), 'Stock initial', null, 'stock_initial');
            }
        });

        return back()->with('succes', 'Intrant créé.');
    }

    public function show(Intrant $intrant)
    {
        $this->authorize('view', $intrant);

        return view('intrants.show', [
            'intrant' => $intrant,
            'mouvements' => $intrant->mouvements()->with(['user', 'campagne'])->latest('date_mouvement')->latest('id')->paginate(15),
        ]);
    }
}
