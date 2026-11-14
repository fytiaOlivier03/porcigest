<?php

namespace App\Jobs;

use App\Models\Campagne;
use App\Services\FinanceService;
use App\Services\ZootechnieService;
use App\Support\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/** Génération différée (queue) du rapport de fin de campagne. */
class GenererRapportCampagne implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $campagneId) {}

    public function handle(ZootechnieService $zoo, FinanceService $finance): void
    {
        $c = Campagne::withoutGlobalScopes()->with('unite')->findOrFail($this->campagneId);
        $i = $zoo->indicateurs($c);
        $f = $finance->totaux($c->id);

        $lignes = [
            "RAPPORT DE CAMPAGNE : {$c->nom}",
            "Unité : {$c->unite->nom} ({$c->unite->code})",
            "Période : {$c->date_debut->format('d/m/Y')} → ".($c->date_fin_reelle?->format('d/m/Y') ?? 'en cours'),
            '',
            "Effectif initial : {$c->effectif_initial}",
            "Effectif actuel  : {$i['effectif_actuel']}",
            "Mortalité        : {$i['morts']} ({$i['taux_mortalite']} %)",
            "Aliment consommé : {$i['aliment_total']} kg",
            'Gain de biomasse : '.($i['gain_biomasse'] ?? 'n/d').' kg',
            'Indice de consommation : '.($i['ic'] ?? 'n/d'),
            '',
            'Dépenses : '.number_format($f['depenses'], 0, ',', ' ').' Ar',
            'Recettes : '.number_format($f['recettes'], 0, ',', ' ').' Ar',
            'Marge    : '.number_format($f['marge'], 0, ',', ' ').' Ar',
            '',
            'Généré le '.now()->format('d/m/Y H:i'),
        ];

        Storage::put("rapports/campagne-{$c->id}.txt", implode("\n", $lignes));
        Audit::info('Rapport de campagne généré', ['campagne_id' => $c->id]);
    }
}
