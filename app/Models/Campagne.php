<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campagne extends Model
{
    use HasFactory, BelongsToExploitation;

    public const STATUTS = ['planifiee', 'en_cours', 'terminee', 'annulee'];

    protected $fillable = [
        'unite_id', 'nom', 'production', 'date_debut', 'date_fin_prevue',
        'date_fin_reelle', 'effectif_initial', 'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin_prevue' => 'date',
            'date_fin_reelle' => 'date',
        ];
    }

    public function unite() { return $this->belongsTo(Unite::class); }
    public function activites() { return $this->hasMany(Activite::class); }
    public function distributions() { return $this->hasMany(DistributionAliment::class); }
    public function pesees() { return $this->hasMany(Pesee::class); }
    public function mortalites() { return $this->hasMany(Mortalite::class); }
    public function productions() { return $this->hasMany(Production::class); }
    public function depenses() { return $this->hasMany(Depense::class); }
    public function recettes() { return $this->hasMany(Recette::class); }
}
