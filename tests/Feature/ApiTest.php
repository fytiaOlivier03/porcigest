<?php

namespace Tests\Feature;

use App\Models\Campagne;
use App\Models\Intrant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreeFermes;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase, CreeFermes;

    public function test_api_protegee_par_authentification(): void
    {
        $this->getJson('/api/intrants')->assertUnauthorized();
    }

    public function test_login_api_retourne_un_token_et_refuse_un_mauvais_mot_de_passe(): void
    {
        $f = $this->ferme();

        $this->postJson('/api/login', ['email' => $f['agent']->email, 'password' => 'password'])
            ->assertOk()->assertJsonStructure(['token']);
        $this->postJson('/api/login', ['email' => $f['agent']->email, 'password' => 'faux'])->assertUnauthorized();
    }

    public function test_liste_et_consultation_limitees_a_son_exploitation(): void
    {
        $a = $this->ferme();
        $b = $this->ferme();
        $mien = Intrant::factory()->create(['exploitation_id' => $a['e']->id, 'nom' => 'Mon aliment']);
        $autre = Intrant::factory()->create(['exploitation_id' => $b['e']->id, 'nom' => 'Aliment voisin']);

        Sanctum::actingAs($a['agent']);

        $this->getJson('/api/intrants')->assertOk()
            ->assertJsonFragment(['nom' => 'Mon aliment'])->assertJsonMissing(['nom' => 'Aliment voisin']);
        $this->getJson("/api/intrants/{$mien->id}")->assertOk()->assertJsonPath('data.nom', 'Mon aliment');
        $this->getJson("/api/intrants/{$autre->id}")->assertNotFound();
    }

    public function test_creation_dune_distribution_retourne_201_et_met_a_jour_le_stock(): void
    {
        $f = $this->ferme();
        $intrant = Intrant::factory()->create(['exploitation_id' => $f['e']->id, 'quantite_disponible' => 100]);
        $campagne = Campagne::factory()->create(['exploitation_id' => $f['e']->id]);
        Sanctum::actingAs($f['agent']);

        $this->postJson('/api/distributions', [
            'campagne_id' => $campagne->id, 'intrant_id' => $intrant->id, 'quantite' => 40,
            'nombre_porcs' => 20, 'date_distribution' => now()->toDateString(),
        ])->assertCreated()->assertJsonPath('data.stock_restant', 60);
    }

    public function test_api_422_si_validation_ou_stock_insuffisant(): void
    {
        $f = $this->ferme();
        $intrant = Intrant::factory()->create(['exploitation_id' => $f['e']->id, 'quantite_disponible' => 10]);
        $campagne = Campagne::factory()->create(['exploitation_id' => $f['e']->id]);
        Sanctum::actingAs($f['agent']);

        $this->postJson('/api/distributions', [])->assertUnprocessable()->assertJsonValidationErrors(['campagne_id', 'quantite']);

        $this->postJson('/api/distributions', [
            'campagne_id' => $campagne->id, 'intrant_id' => $intrant->id, 'quantite' => 50,
            'nombre_porcs' => 20, 'date_distribution' => now()->toDateString(),
        ])->assertUnprocessable()->assertJsonFragment(['message' => 'Stock insuffisant pour « '.$intrant->nom.' » : 10 '.$intrant->unite_mesure.' disponible(s), 50 demandé(s).']);

        $this->assertEquals(10, $intrant->fresh()->quantite_disponible);
    }
}
