<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Intrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Intrant> */
class IntrantFactory extends Factory
{
    protected $model = Intrant::class;

    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'nom' => 'Provende '.fake()->unique()->word(),
            'categorie' => 'aliment',
            'unite_mesure' => 'kg',
            'quantite_disponible' => 100,   // la factory est "unguarded" : permet de fixer un stock de test
            'seuil_minimum' => 20,
            'cout_unitaire' => 2500,
        ];
    }
}
