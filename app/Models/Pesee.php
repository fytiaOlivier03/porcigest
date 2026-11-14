<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;

class Pesee extends Model
{
    use BelongsToExploitation;

    protected $fillable = ['campagne_id', 'user_id', 'date_pesee', 'poids_moyen_kg', 'effectif_pese'];

    protected function casts(): array
    {
        return ['date_pesee' => 'date', 'poids_moyen_kg' => 'float'];
    }

    public function campagne() { return $this->belongsTo(Campagne::class); }
}
