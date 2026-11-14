<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;

class Mortalite extends Model
{
    use BelongsToExploitation;

    protected $fillable = ['campagne_id', 'user_id', 'date_mortalite', 'nombre', 'cause'];

    protected function casts(): array
    {
        return ['date_mortalite' => 'date'];
    }

    public function campagne() { return $this->belongsTo(Campagne::class); }
}
