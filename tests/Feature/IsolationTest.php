<?php

namespace Tests\Feature;

use App\Models\Campagne;
use App\Models\Intrant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreeFermes;
use Tests\TestCase;

/** Règle critique : une exploitation ne voit jamais les données d'une autre. */
class IsolationTest extends TestCase
{
    use RefreshDatabase, CreeFermes;

    public function test_une_exploitation_ne_peut_pas_consulter_les_donnees_dune_autre(): void
    {
        $a = $this->ferme();
        $b = $this->ferme();
        $campagneA = Campagne::factory()->create(['exploitation_id' => $a['e']->id, 'nom' => 'Lot-secret-A']);

        $this->actingAs($b['responsable'])->get(route('campagnes.show', $campagneA))->assertNotFound();
        $this->actingAs($b['responsable'])->get(route('campagnes.index'))->assertOk()->assertDontSee('Lot-secret-A');
        $this->actingAs($a['responsable'])->get(route('campagnes.show', $campagneA))->assertOk();
    }

    public function test_impossible_de_modifier_le_stock_dune_autre_exploitation(): void
    {
        $a = $this->ferme();
        $b = $this->ferme();
        $intrantA = Intrant::factory()->create(['exploitation_id' => $a['e']->id, 'quantite_disponible' => 50]);

        $this->actingAs($b['admin'])->post(route('intrants.mouvements.store', $intrantA), [
            'type' => 'entree', 'quantite' => 10, 'date_mouvement' => now()->toDateString(),
        ])->assertNotFound();

        $this->assertEquals(50, Intrant::withoutGlobalScopes()->find($intrantA->id)->quantite_disponible);
    }

    public function test_un_admin_ne_peut_pas_desactiver_le_compte_dune_autre_exploitation(): void
    {
        $a = $this->ferme();
        $b = $this->ferme();

        $this->actingAs($b['admin'])->post(route('users.toggle', $a['agent']))->assertNotFound();
        $this->assertTrue($a['agent']->fresh()->actif);
    }

    public function test_les_identifiants_dune_autre_exploitation_sont_rejetes_par_la_validation(): void
    {
        $a = $this->ferme();
        $b = $this->ferme();
        $campagneB = Campagne::factory()->create(['exploitation_id' => $b['e']->id]);
        $intrantA = Intrant::factory()->create(['exploitation_id' => $a['e']->id]);

        $this->actingAs($a['agent'])->post(route('distributions.store'), [
            'campagne_id' => $campagneB->id, 'intrant_id' => $intrantA->id, 'quantite' => 5,
            'nombre_porcs' => 5, 'date_distribution' => now()->toDateString(),
        ])->assertSessionHasErrors('campagne_id');
    }
}
