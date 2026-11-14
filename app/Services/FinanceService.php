<?php

namespace App\Services;

use App\Models\Depense;
use App\Models\Recette;

class FinanceService
{
    /** Marge simplifiée = Recettes − Dépenses (globale ou pour une campagne). */
    public function totaux(?int $campagneId = null): array
    {
        $depenses = (float) Depense::query()->when($campagneId, fn ($q) => $q->where('campagne_id', $campagneId))->sum('montant');
        $recettes = (float) Recette::query()->when($campagneId, fn ($q) => $q->where('campagne_id', $campagneId))->sum('montant');

        return [
            'depenses' => $depenses,
            'recettes' => $recettes,
            'marge' => round($recettes - $depenses, 2),
        ];
    }
}
