<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntrantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'categorie' => $this->categorie,
            'unite_mesure' => $this->unite_mesure,
            'quantite_disponible' => $this->quantite_disponible,
            'seuil_minimum' => $this->seuil_minimum,
            'critique' => $this->isCritique(),
        ];
    }
}
