<?php

namespace Database\Factories;

use App\Models\Campagne;
use App\Models\Exploitation;
use App\Models\Unite;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Campagne> */
class CampagneFactory extends Factory
{
    protected $model = Campagne::class;

    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            // l'unité appartient à la même exploitation que la campagne
            'unite_id' => fn (array $attrs) => Unite::factory()->create(['exploitation_id' => $attrs['exploitation_id']])->id,
            'nom' => 'Lot '.fake()->unique()->numerify('2026-###'),
            'production' => 'Porcs charcutiers',
            'date_debut' => now()->subDays(30)->toDateString(),
            'date_fin_prevue' => now()->addDays(90)->toDateString(),
            'effectif_initial' => 30,
            'statut' => 'en_cours',
        ];
    }
}
