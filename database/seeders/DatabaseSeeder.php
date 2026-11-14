<?php

namespace Database\Seeders;

use App\Exceptions\StockInsuffisantException;
use App\Models\Activite;
use App\Models\Campagne;
use App\Models\Depense;
use App\Models\Exploitation;
use App\Models\Intrant;
use App\Models\Mortalite;
use App\Models\Pesee;
use App\Models\Production;
use App\Models\Recette;
use App\Models\Unite;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

/**
 * Jeu de données de démonstration : DEUX exploitations porcines (Antsirabe et Fianarantsoa)
 * pour démontrer l'isolation des données. Mot de passe de tous les comptes : "password".
 * Les mouvements passent par StockService : le stock reste cohérent avec l'historique.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->peupler([
            'nom' => 'Ferme Soamandrakizay', 'localisation' => 'Antsirabe', 'capacite' => 300,
            'users' => [
                ['admin', 'Rakoto Jean', 'admin@soamandrakizay.mg'],
                ['responsable', 'Rasoanaivo Hery', 'hery@soamandrakizay.mg'],
                ['agent', 'Randria Faly', 'faly@soamandrakizay.mg'],
                ['agent', 'Rabe Tiana', 'tiana@soamandrakizay.mg'],
            ],
            'unites' => [
                ['P01', 'Porcherie Nord', 'engraissement', 60], ['P02', 'Porcherie Sud', 'engraissement', 60],
                ['M01', 'Maternité', 'maternite', 20], ['Q01', 'Quarantaine', 'quarantaine', 10],
            ],
            // nom, catégorie, unité, seuil, coût, stock initial, achat complémentaire
            'intrants' => [
                ['Provende croissance', 'aliment', 'kg', 800, 2600, 5000, 5000],
                ['Provende finition', 'aliment', 'kg', 600, 2400, 4000, 3000],
                ['Son de riz', 'aliment', 'kg', 100, 900, 600, 0],
                ['Vermifuge ivermectine', 'medicament', 'dose', 20, 1500, 25, 0],
                ['Fer injectable', 'produit_veterinaire', 'mL', 100, 800, 400, 0],
                ['Désinfectant', 'consommable', 'L', 10, 6000, 40, 0],
            ],
            'lots' => [
                ['Lot 2026-01 Charcutiers', 'P01', 70, 40, 'en_cours', 'Provende croissance', 1.9, 25, 0.75, [[40, 1, 'Diarrhée'], [18, 1, 'Écrasement']]],
                ['Lot 2026-02 Engraissement', 'P02', 32, 45, 'en_cours', 'Provende finition', 1.9, 22, 0.7, [[20, 2, 'Pneumonie'], [8, 1, 'Maladie']]],
                ['Lot 2025-04 Charcutiers', 'M01', 150, 18, 'terminee', 'Provende croissance', 1.6, 24, 0.6, [[90, 1, 'Maladie']]],
            ],
            'ajustement' => ['Vermifuge ivermectine', -8, 'Casse de flacons'],
            'ventes' => [['Lot 2025-04 Charcutiers', 17, 1720, 15300000]],
            'charges' => [['main_oeuvre', 'Salaires du mois', 600000], ['energie', 'Électricité JIRAMA', 180000], ['transport', 'Transport provende', 120000]],
        ]);

        $this->peupler([
            'nom' => 'Porcherie Mandrosoa', 'localisation' => 'Fianarantsoa', 'capacite' => 120,
            'users' => [
                ['admin', 'Andrianina Voahangy', 'admin@mandrosoa.mg'],
                ['responsable', 'Razafy Mino', 'mino@mandrosoa.mg'],
                ['agent', 'Ravelo Toky', 'toky@mandrosoa.mg'],
            ],
            'unites' => [['B1', 'Bâtiment principal', 'engraissement', 40], ['B2', 'Petit bâtiment', 'post_sevrage', 25]],
            'intrants' => [
                ['Aliment porc complet', 'aliment', 'kg', 300, 2700, 2500, 1500],
                ['Antibiotique oxytétracycline', 'medicament', 'mL', 50, 900, 300, 0],
            ],
            'lots' => [
                ['Bande 2026-A', 'B1', 45, 30, 'en_cours', 'Aliment porc complet', 1.8, 26, 0.72, [[12, 1, 'Maladie']]],
            ],
            'ajustement' => null,
            'ventes' => [],
            'charges' => [['main_oeuvre', 'Salaire gardien', 250000], ['energie', 'Électricité', 70000]],
        ]);

        Auth::forgetGuards();
    }

    private function peupler(array $c): void
    {
        $stock = app(StockService::class);
        $jour = fn (int $n) => now()->subDays($n)->toDateString();

        $exploitation = Exploitation::create([
            'nom' => $c['nom'], 'localisation' => $c['localisation'], 'type' => 'elevage_porcin',
            'capacite' => $c['capacite'], 'statut' => 'actif',
        ]);

        $users = [];
        foreach ($c['users'] as [$role, $nom, $email]) {
            $users[] = User::factory()->create([
                'exploitation_id' => $exploitation->id, 'role' => $role, 'name' => $nom, 'email' => $email,
            ]);
        }
        $admin = $users[0];
        $responsable = $users[1];
        $exploitation->update(['responsable_id' => $responsable->id]);

        // À partir d'ici, on agit "en tant que" l'administrateur : le scope d'exploitation s'applique.
        Auth::setUser($admin);

        $unites = [];
        foreach ($c['unites'] as [$code, $nom, $type, $capacite]) {
            $unites[$code] = Unite::create(['code' => $code, 'nom' => $nom, 'type' => $type, 'capacite' => $capacite, 'localisation' => 'Site principal', 'etat' => 'disponible']);
        }

        $intrants = [];
        foreach ($c['intrants'] as [$nom, $cat, $unite, $seuil, $cout, $initial, $achat]) {
            $i = Intrant::create(['nom' => $nom, 'categorie' => $cat, 'unite_mesure' => $unite, 'seuil_minimum' => $seuil, 'cout_unitaire' => $cout]);
            $stock->entree($i->id, $initial, $admin, 'Stock initial', $jour(160), 'stock_initial');
            if ($achat > 0) {
                $stock->entree($i->id, $achat, $responsable, 'Achat fournisseur', $jour(75));
                Depense::create(['user_id' => $responsable->id, 'categorie' => 'intrants', 'libelle' => "Achat {$nom}", 'montant' => $achat * $cout, 'date_depense' => $jour(75)]);
            }
            $intrants[$nom] = $i;
        }

        $agents = array_values(array_filter($users, fn ($u) => $u->role === 'agent'));
        $campagnes = [];

        foreach ($c['lots'] as [$nom, $codeUnite, $debut, $effectif, $statut, $aliment, $kgJour, $poidsDepart, $gainJour, $morts]) {
            // Créée "en_cours" pour pouvoir distribuer, puis clôturée si besoin.
            $camp = Campagne::create([
                'unite_id' => $unites[$codeUnite]->id, 'nom' => $nom, 'production' => 'Porcs charcutiers',
                'date_debut' => $jour($debut), 'date_fin_prevue' => $jour($debut - 120), 'effectif_initial' => $effectif,
                'statut' => 'en_cours',
            ]);
            $campagnes[$nom] = $camp;
            $unites[$codeUnite]->update(['etat' => 'occupee']);

            // Pesées tous les 20 jours
            for ($d = $debut; $d >= 1; $d -= 20) {
                Pesee::create([
                    'campagne_id' => $camp->id, 'user_id' => $agents[0]->id, 'date_pesee' => $jour($d),
                    'poids_moyen_kg' => round($poidsDepart + $gainJour * ($debut - $d), 1), 'effectif_pese' => min($effectif, 15),
                ]);
            }
            foreach ($morts as [$il, $n, $cause]) {
                Mortalite::create(['campagne_id' => $camp->id, 'user_id' => $agents[0]->id, 'date_mortalite' => $jour($il), 'nombre' => $n, 'cause' => $cause]);
            }

            // Une distribution tous les 4 jours (= 4 jours de ration). Si le stock est insuffisant, la règle métier refuse.
            for ($d = $debut; $d >= 1; $d -= 4) {
                try {
                    $stock->distribuerAliment([
                        'campagne_id' => $camp->id, 'intrant_id' => $intrants[$aliment]->id,
                        'quantite' => round($effectif * $kgJour * 4, 1), 'nombre_porcs' => $effectif,
                        'date_distribution' => $jour($d), 'observation' => 'Ration de 4 jours',
                    ], $agents[$d % count($agents)]);
                } catch (StockInsuffisantException) {
                    // stock épuisé : distribution refusée (comportement attendu)
                }
            }

            // Quelques activités techniques
            foreach ([['nettoyage', 2], ['vaccination', 3], ['pesee', 4], ['entretien', 5]] as $k => [$type, $div]) {
                if ($debut - $k * 12 > 0) {
                    Activite::create([
                        'campagne_id' => $camp->id, 'user_id' => $agents[$k % count($agents)]->id, 'type' => $type,
                        'date_activite' => $jour($debut - $k * 12), 'description' => ucfirst($type).' du lot '.$nom, 'cout' => $div * 15000,
                    ]);
                }
            }

            if ($statut === 'terminee') {
                $camp->update(['statut' => 'terminee', 'date_fin_reelle' => $jour($debut - 120 + 5)]);
                $unites[$codeUnite]->update(['etat' => 'disponible']);
            }
        }

        foreach ($c['ventes'] as [$lot, $nb, $poids, $montant]) {
            $camp = $campagnes[$lot];
            Production::create(['campagne_id' => $camp->id, 'date_production' => $jour(30), 'nombre_porcs' => $nb, 'poids_total_kg' => $poids, 'observation' => 'Vente de fin de lot']);
            Recette::create(['campagne_id' => $camp->id, 'user_id' => $responsable->id, 'categorie' => 'vente_porcs', 'libelle' => "Vente de {$nb} porcs", 'montant' => $montant, 'date_recette' => $jour(30)]);
        }

        foreach ($c['charges'] as $k => [$cat, $libelle, $montant]) {
            Depense::create(['user_id' => $responsable->id, 'categorie' => $cat, 'libelle' => $libelle, 'montant' => $montant, 'date_depense' => $jour(5 + $k)]);
        }

        // Ajustement d'inventaire qui fait passer un intrant sous son seuil → événement StockCritical → notification
        if ($c['ajustement']) {
            [$nom, $delta, $motif] = $c['ajustement'];
            $stock->ajuster($intrants[$nom]->id, $delta, $responsable, $motif, $jour(2));
        }
    }
}
