<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campagne_id' => $this->campagne_id,
            'intrant' => $this->whenLoaded('intrant', fn () => $this->intrant->nom),
            'quantite' => $this->quantite,
            'nombre_porcs' => $this->nombre_porcs,
            'date_distribution' => $this->date_distribution->toDateString(),
            'stock_restant' => $this->whenLoaded('intrant', fn () => $this->intrant->quantite_disponible),
        ];
    }
}
