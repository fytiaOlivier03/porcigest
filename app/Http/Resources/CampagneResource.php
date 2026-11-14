<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampagneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'production' => $this->production,
            'statut' => $this->statut,
            'date_debut' => $this->date_debut->toDateString(),
            'date_fin_prevue' => $this->date_fin_prevue->toDateString(),
            'effectif_initial' => $this->effectif_initial,
            'unite' => $this->whenLoaded('unite', fn () => ['code' => $this->unite->code, 'nom' => $this->unite->nom]),
        ];
    }
}
