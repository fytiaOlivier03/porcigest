<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;

class DistributionAliment extends Model
{
    use BelongsToExploitation;

    protected $table = 'distributions_aliment';

    protected $fillable = [
        'campagne_id', 'intrant_id', 'mouvement_stock_id', 'user_id',
        'date_distribution', 'quantite', 'nombre_porcs', 'observation',
    ];

    protected function casts(): array
    {
        return ['date_distribution' => 'date', 'quantite' => 'float'];
    }

    public function campagne() { return $this->belongsTo(Campagne::class); }
    public function intrant() { return $this->belongsTo(Intrant::class); }
    public function mouvement() { return $this->belongsTo(MouvementStock::class, 'mouvement_stock_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
