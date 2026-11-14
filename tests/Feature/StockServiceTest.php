<?php

namespace Tests\Feature;

use App\Events\StockCritical;
use App\Exceptions\StockInsuffisantException;
use App\Models\Campagne;
use App\Models\Depense;
use App\Models\Intrant;
use App\Models\MouvementStock;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\Concerns\CreeFermes;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase, CreeFermes;

    private function donnees(array $f, array $intrant = []): array
    {
        $this->actingAs($f['agent']);

        return [
            Intrant::factory()->create($intrant + ['exploitation_id' => $f['e']->id]),
            Campagne::factory()->create(['exploitation_id' => $f['e']->id]),
        ];
    }

    /** Test de fonctionnalité métier + transaction. */
    public function test_distribution_diminue_le_stock_cree_mouvement_et_depense(): void
    {
        $f = $this->ferme();
        [$intrant, $campagne] = $this->donnees($f, ['quantite_disponible' => 100, 'cout_unitaire' => 2000, 'seuil_minimum' => 10]);

        $d = app(StockService::class)->distribuerAliment([
            'campagne_id' => $campagne->id, 'intrant_id' => $intrant->id,
            'quantite' => 30, 'nombre_porcs' => 25, 'date_distribution' => now()->toDateString(),
        ], $f['agent']);

        $this->assertEquals(70, $intrant->fresh()->quantite_disponible);
        $this->assertEquals($campagne->id, $d->campagne_id);
        $mouvement = MouvementStock::first();
        $this->assertEquals(-30, $mouvement->quantite);
        $this->assertEquals(100, $mouvement->quantite_avant);
        $this->assertEquals(70, $mouvement->quantite_apres);
        $this->assertEquals(60000, Depense::first()->montant);   // 30 kg × 2000 Ar
    }

    /** Test d'une règle critique : jamais de stock négatif + rollback complet. */
    public function test_stock_jamais_negatif_et_rollback(): void
    {
        $f = $this->ferme();
        [$intrant, $campagne] = $this->donnees($f, ['quantite_disponible' => 100]);

        try {
            app(StockService::class)->distribuerAliment([
                'campagne_id' => $campagne->id, 'intrant_id' => $intrant->id,
                'quantite' => 150, 'nombre_porcs' => 25, 'date_distribution' => now()->toDateString(),
            ], $f['agent']);
            $this->fail('StockInsuffisantException attendue.');
        } catch (StockInsuffisantException $e) {
            $this->assertStringContainsString('Stock insuffisant', $e->getMessage());
        }

        $this->assertEquals(100, $intrant->fresh()->quantite_disponible);
        $this->assertSame(0, MouvementStock::count());
        $this->assertSame(0, Depense::count());
    }

    public function test_evenement_stock_critique_quand_le_seuil_est_franchi(): void
    {
        Event::fake([StockCritical::class]);
        $f = $this->ferme();
        [$intrant, $campagne] = $this->donnees($f, ['quantite_disponible' => 50, 'seuil_minimum' => 20]);

        app(StockService::class)->distribuerAliment([
            'campagne_id' => $campagne->id, 'intrant_id' => $intrant->id,
            'quantite' => 35, 'nombre_porcs' => 10, 'date_distribution' => now()->toDateString(),
        ], $f['agent']);

        Event::assertDispatched(StockCritical::class);
    }

    public function test_distribution_refusee_si_campagne_terminee(): void
    {
        $f = $this->ferme();
        [$intrant, $campagne] = $this->donnees($f);
        $campagne->update(['statut' => 'terminee']);

        $this->expectException(\DomainException::class);
        app(StockService::class)->distribuerAliment([
            'campagne_id' => $campagne->id, 'intrant_id' => $intrant->id,
            'quantite' => 5, 'nombre_porcs' => 5, 'date_distribution' => now()->toDateString(),
        ], $f['agent']);
    }
}
