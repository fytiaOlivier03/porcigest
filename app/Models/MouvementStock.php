<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use BelongsToExploitation;

    protected $table = 'mouvements_stock';

    public const TYPES = ['stock_initial', 'entree', 'sortie', 'consommation', 'ajustement'];

    protected $fillable = [
        'intrant_id', 'campagne_id', 'user_id', 'type', 'quantite',
        'quantite_avant', 'quantite_apres', 'motif', 'date_mouvement',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'float',
            'quantite_avant' => 'float',
            'quantite_apres' => 'float',
            'date_mouvement' => 'date',
        ];
    }

    public function intrant() { return $this->belongsTo(Intrant::class); }
    public function campagne() { return $this->belongsTo(Campagne::class); }
    public function user() { return $this->belongsTo(User::class); }
}
