<?php

namespace App\Services;

use App\Events\StockCritical;
use App\Exceptions\StockInsuffisantException;
use App\Models\Campagne;
use App\Models\Depense;
use App\Models\DistributionAliment;
use App\Models\Intrant;
use App\Models\MouvementStock;
use App\Models\User;
use App\Support\Audit;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Unique point d'entrée pour modifier un stock.
 * Garantit : (1) jamais de stock négatif, (2) traçabilité de chaque mouvement,
 * (3) atomicité (transaction), (4) absence de course critique (verrou pessimiste).
 */
class StockService
{
    /** Entrée en stock (ou stock initial). */
    public function entree(int $intrantId, float $quantite, User $user, ?string $motif = null, ?string $date = null, string $type = 'entree'): MouvementStock
    {
        $res = DB::transaction(
            fn () => $this->appliquer($intrantId, $type, abs($quantite), $user, null, $motif, $date),
            3
        );
        $this->apres($res['intrant'], $res['avant']);

        return $res['mouvement'];
    }

    /** Ajustement d'inventaire : quantité signée (+ ou -). */
    public function ajuster(int $intrantId, float $delta, User $user, string $motif, ?string $date = null): MouvementStock
    {
        $res = DB::transaction(
            fn () => $this->appliquer($intrantId, 'ajustement', $delta, $user, null, $motif, $date),
            3
        );
        $this->apres($res['intrant'], $res['avant']);

        return $res['mouvement'];
    }

    /**
     * Distribution d'aliment à une campagne — TRANSACTION OBLIGATOIRE :
     *  1. vérifier campagne / intrant            4. enregistrer la distribution (rattachée à la campagne)
     *  2. verrouiller l'intrant + contrôler      5. enregistrer la dépense d'alimentation
     *  3. créer le mouvement + diminuer le stock
     * Si une étape échoue : ROLLBACK de l'ensemble.
     */
    public function distribuerAliment(array $data, User $user): DistributionAliment
    {
        $res = DB::transaction(function () use ($data, $user) {
            $campagne = Campagne::findOrFail($data['campagne_id']);
            if ($campagne->statut !== 'en_cours') {
                throw new DomainException("La campagne « {$campagne->nom} » n'est pas en cours.");
            }

            $intrant = Intrant::findOrFail($data['intrant_id']);
            if ($intrant->categorie !== 'aliment') {
                throw new DomainException("« {$intrant->nom} » n'est pas un aliment.");
            }

            $quantite = round((float) $data['quantite'], 2);
            $r = $this->appliquer(
                $intrant->id, 'consommation', -$quantite, $user,
                $campagne->id, 'Distribution aliment', $data['date_distribution']
            );

            $distribution = DistributionAliment::create([
                'campagne_id' => $campagne->id,
                'intrant_id' => $intrant->id,
                'mouvement_stock_id' => $r['mouvement']->id,
                'user_id' => $user->id,
                'date_distribution' => $data['date_distribution'],
                'quantite' => $quantite,
                'nombre_porcs' => $data['nombre_porcs'],
                'observation' => $data['observation'] ?? null,
            ]);

            Depense::create([
                'campagne_id' => $campagne->id,
                'user_id' => $user->id,
                'categorie' => 'alimentation',
                'libelle' => "Aliment {$intrant->nom} ({$quantite} {$intrant->unite_mesure})",
                'montant' => round($quantite * $intrant->cout_unitaire, 2),
                'date_depense' => $data['date_distribution'],
            ]);

            return $r + ['distribution' => $distribution];
        }, 3);   // 3 tentatives en cas d'interblocage (deadlock)

        $this->apres($res['intrant'], $res['avant']);

        return $res['distribution'];
    }

    /**
     * Cœur de la règle « stock jamais négatif » ET de la gestion de concurrence.
     * Doit être appelé DANS une transaction.
     *
     * lockForUpdate() = SELECT ... FOR UPDATE : si deux utilisateurs veulent consommer
     * les 5 derniers sacs en même temps, la 2e transaction attend la fin de la 1re,
     * relit le stock à jour (0) et est refusée.
     */
    private function appliquer(int $intrantId, string $type, float $delta, User $user, ?int $campagneId, ?string $motif, ?string $date): array
    {
        $intrant = Intrant::lockForUpdate()->findOrFail($intrantId);

        $avant = (float) $intrant->quantite_disponible;
        $apres = round($avant + $delta, 2);

        if ($apres < 0) {
            throw new StockInsuffisantException($intrant, $avant, abs($delta));
        }

        $intrant->quantite_disponible = $apres;
        $intrant->save();

        $mouvement = MouvementStock::create([
            'intrant_id' => $intrant->id,
            'campagne_id' => $campagneId,
            'user_id' => $user->id,
            'type' => $type,
            'quantite' => $delta,
            'quantite_avant' => $avant,
            'quantite_apres' => $apres,
            'motif' => $motif,
            'date_mouvement' => $date ?? now()->toDateString(),
        ]);

        return ['mouvement' => $mouvement, 'intrant' => $intrant, 'avant' => $avant];
    }

    /** Après COMMIT : journalisation + événement métier si le seuil vient d'être franchi. */
    private function apres(Intrant $intrant, float $avant): void
    {
        Audit::info('Mouvement de stock', [
            'intrant' => $intrant->nom, 'avant' => $avant, 'apres' => $intrant->quantite_disponible,
        ]);

        if ($avant > $intrant->seuil_minimum && $intrant->isCritique()) {
            event(new StockCritical($intrant));
        }
    }
}
