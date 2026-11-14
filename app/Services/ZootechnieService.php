<?php

namespace App\Services;

use App\Models\Campagne;

/**
 * MODULE PERSONNEL : suivi zootechnique d'un lot de porcs.
 *  - taux de mortalité (+ alerte au-delà de 5 %)
 *  - indice de consommation IC = aliment consommé (kg) / gain de biomasse (kg)
 *    (plus l'IC est bas, plus l'élevage est rentable)
 */
class ZootechnieService
{
    public const SEUIL_MORTALITE = 5.0;   // en %

    public function indicateurs(Campagne $c): array
    {
        $morts = (int) $c->mortalites()->sum('nombre');
        $effectif = max($c->effectif_initial - $morts, 0);
        $taux = $c->effectif_initial > 0 ? round($morts / $c->effectif_initial * 100, 1) : 0.0;
        $aliment = (float) $c->distributions()->sum('quantite');

        $premiere = $c->pesees()->orderBy('date_pesee')->orderBy('id')->first();
        $derniere = $c->pesees()->orderByDesc('date_pesee')->orderByDesc('id')->first();

        $gain = null;
        $ic = null;
        if ($premiere && $derniere && $premiere->id !== $derniere->id) {
            // Approximation : biomasse = poids moyen × effectif
            $gain = round($derniere->poids_moyen_kg * $effectif - $premiere->poids_moyen_kg * $c->effectif_initial, 2);
            if ($gain > 0) {
                $ic = round($aliment / $gain, 2);
            }
        }

        return [
            'effectif_actuel' => $effectif,
            'morts' => $morts,
            'taux_mortalite' => $taux,
            'alerte_mortalite' => $taux > self::SEUIL_MORTALITE,
            'aliment_total' => $aliment,
            'gain_biomasse' => $gain,
            'ic' => $ic,
        ];
    }
}
