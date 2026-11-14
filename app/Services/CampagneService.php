<?php

namespace App\Services;

use App\Events\CampagneCompleted;
use App\Models\Campagne;
use App\Models\Unite;
use DomainException;
use Illuminate\Support\Facades\DB;

class CampagneService
{
    public function creer(array $data): Campagne
    {
        return DB::transaction(function () use ($data) {
            $campagne = Campagne::create($data);

            if ($campagne->statut === 'en_cours') {
                $campagne->unite->update(['etat' => 'occupee']);
            }

            return $campagne;
        });
    }

    public function terminer(Campagne $campagne): void
    {
        if ($campagne->statut !== 'en_cours') {
            throw new DomainException('Seule une campagne en cours peut être terminée.');
        }

        DB::transaction(function () use ($campagne) {
            $campagne->update(['statut' => 'terminee', 'date_fin_reelle' => now()->toDateString()]);
            $campagne->unite->update(['etat' => 'disponible']);
        });

        event(new CampagneCompleted($campagne));
    }
}
