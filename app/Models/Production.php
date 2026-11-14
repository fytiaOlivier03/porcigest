<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use BelongsToExploitation;

    protected $fillable = ['campagne_id', 'date_production', 'nombre_porcs', 'poids_total_kg', 'observation'];

    protected function casts(): array
    {
        return ['date_production' => 'date', 'poids_total_kg' => 'float'];
    }

    public function campagne() { return $this->belongsTo(Campagne::class); }
}
