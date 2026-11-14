<?php

namespace App\Http\Requests;

use App\Models\DistributionAliment;

/** Partagée entre l'interface web et l'API REST. */
class StoreDistributionRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DistributionAliment::class);
    }

    public function rules(): array
    {
        return [
            'campagne_id' => ['required', $this->existeChezMoi('campagnes')],
            'intrant_id' => ['required', $this->existeChezMoi('intrants')],
            'quantite' => ['required', 'numeric', 'gt:0', 'max:100000'],
            'nombre_porcs' => ['required', 'integer', 'min:1'],
            'date_distribution' => ['required', 'date', 'before_or_equal:today'],
            'observation' => ['nullable', 'string', 'max:200'],
        ];
    }
}
