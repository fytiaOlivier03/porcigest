<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;

class Recette extends Model
{
    use BelongsToExploitation;

    public const CATEGORIES = ['vente_porcs', 'vente_porcelets', 'vente_fumier', 'autre'];

    protected $fillable = ['campagne_id', 'user_id', 'categorie', 'libelle', 'montant', 'date_recette'];

    protected function casts(): array
    {
        return ['date_recette' => 'date', 'montant' => 'float'];
    }

    public function campagne() { return $this->belongsTo(Campagne::class); }
    public function user() { return $this->belongsTo(User::class); }
}
