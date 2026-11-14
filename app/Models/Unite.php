<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unite extends Model
{
    use HasFactory, BelongsToExploitation;

    public const TYPES = ['maternite', 'engraissement', 'quarantaine', 'post_sevrage'];

    protected $fillable = ['code', 'nom', 'type', 'capacite', 'localisation', 'etat'];

    public function campagnes()
    {
        return $this->hasMany(Campagne::class);
    }
}
