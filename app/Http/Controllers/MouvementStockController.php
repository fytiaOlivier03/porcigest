<?php

namespace App\Http\Controllers;

use App\Exceptions\StockInsuffisantException;
use App\Http\Requests\StoreMouvementRequest;
use App\Models\Intrant;
use App\Services\StockService;

class MouvementStockController extends Controller
{
    public function store(StoreMouvementRequest $request, Intrant $intrant, StockService $stock)
    {
        try {
            if ($request->type === 'entree') {
                $stock->entree($intrant->id, (float) $request->quantite, $request->user(), $request->motif ?? 'Entrée en stock', $request->date_mouvement);
            } else {
                $stock->ajuster($intrant->id, (float) $request->quantite, $request->user(), $request->motif, $request->date_mouvement);
            }
        } catch (StockInsuffisantException $e) {
            return back()->withErrors(['quantite' => $e->getMessage()]);
        }

        return back()->with('succes', 'Mouvement de stock enregistré.');
    }
}
