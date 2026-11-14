<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Unite;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Unite> */
class UniteFactory extends Factory
{
    protected $model = Unite::class;

    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'code' => 'P'.fake()->unique()->numerify('###'),
            'nom' => 'Porcherie '.fake()->word(),
            'type' => 'engraissement',
            'capacite' => 60,
            'localisation' => 'Bloc A',
            'etat' => 'disponible',
        ];
    }
}
