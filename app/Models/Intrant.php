<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intrant extends Model
{
    use HasFactory, BelongsToExploitation;

    public const CATEGORIES = ['aliment', 'medicament', 'produit_veterinaire', 'consommable', 'carburant'];

    // quantite_disponible n'est PAS fillable : seul StockService peut la modifier (traçabilité).
    protected $fillable = ['nom', 'categorie', 'unite_mesure', 'seuil_minimum', 'cout_unitaire'];

    protected function casts(): array
    {
        return [
            'quantite_disponible' => 'float',
            'seuil_minimum' => 'float',
            'cout_unitaire' => 'float',
        ];
    }

    public function isCritique(): bool
    {
        return $this->quantite_disponible <= $this->seuil_minimum;
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }
}
