<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    use BelongsToExploitation;

    public const TYPES = [
        'preparation', 'nettoyage', 'pesee', 'vaccination', 'traitement',
        'alimentation', 'entretien', 'autre',
    ];

    protected $fillable = ['campagne_id', 'user_id', 'type', 'date_activite', 'description', 'cout'];

    protected function casts(): array
    {
        return ['date_activite' => 'date', 'cout' => 'float'];
    }

    public function campagne() { return $this->belongsTo(Campagne::class); }
    public function user() { return $this->belongsTo(User::class); }
}
