<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use BelongsToExploitation;

    public const CATEGORIES = ['intrants', 'alimentation', 'main_oeuvre', 'traitements', 'transport', 'energie', 'autre'];

    protected $fillable = ['campagne_id', 'user_id', 'categorie', 'libelle', 'montant', 'date_depense'];

    protected function casts(): array
    {
        return ['date_depense' => 'date', 'montant' => 'float'];
    }

    public function campagne() { return $this->belongsTo(Campagne::class); }
    public function user() { return $this->belongsTo(User::class); }
}
