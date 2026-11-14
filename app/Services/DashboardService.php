<?php

namespace App\Services;

use App\Models\Campagne;
use App\Models\Intrant;
use App\Models\Unite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct(private FinanceService $finance, private ZootechnieService $zoo) {}

    public static function cle(int $exploitationId): string
    {
        return "dashboard.exploitation.{$exploitationId}";
    }

    /** Appelée par le trait BelongsToExploitation à chaque écriture (saved / deleted). */
    public static function invalider(?int $exploitationId): void
    {
        if ($exploitationId) {
            Cache::forget(self::cle($exploitationId));
        }
    }

    /** Statistiques mises en cache 10 minutes, une clé par exploitation (pas de fuite entre exploitations). */
    public function stats(): array
    {
        $id = Auth::user()->exploitation_id;

        return Cache::remember(self::cle($id), 600, fn () => $this->calculer());
    }

    private function calculer(): array
    {
        $actives = Campagne::where('statut', 'en_cours')->with('unite')->get();
        $intrants = Intrant::orderBy('nom')->get();
        $indic = $actives->map(fn ($c) => $this->zoo->indicateurs($c));
        $ics = $indic->pluck('ic')->filter()->values();
        $finance = $this->finance->totaux();

        return [
            'campagnes_actives' => $actives->count(),
            'nb_unites' => Unite::count(),
            'stocks' => $intrants->map(fn ($i) => [
                'nom' => $i->nom,
                'quantite' => $i->quantite_disponible,
                'unite' => $i->unite_mesure,
                'seuil' => $i->seuil_minimum,
                'critique' => $i->isCritique(),
            ])->all(),
            'depenses' => $finance['depenses'],
            'recettes' => $finance['recettes'],
            'marge' => $finance['marge'],
            'alertes' => [
                'stocks' => $intrants->filter->isCritique()->pluck('nom')->values()->all(),
                'mortalite' => $actives->filter(fn ($c, $k) => $indic[$k]['alerte_mortalite'])->pluck('nom')->values()->all(),
            ],
            // Indicateurs calculés supplémentaires
            'effectif_total' => (int) $indic->sum('effectif_actuel'),
            'taux_mortalite_moyen' => round((float) ($indic->avg('taux_mortalite') ?? 0), 1),
            'ic_moyen' => $ics->isNotEmpty() ? round($ics->avg(), 2) : null,
        ];
    }
}
