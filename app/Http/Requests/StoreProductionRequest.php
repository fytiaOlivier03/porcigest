<?php

namespace App\Http\Requests;

use App\Models\Production;

class StoreProductionRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Production::class);
    }

    public function rules(): array
    {
        return [
            'date_production' => ['required', 'date', 'before_or_equal:today'],
            'nombre_porcs' => ['required', 'integer', 'min:1'],
            'poids_total_kg' => ['required', 'numeric', 'gt:0'],
            'observation' => ['nullable', 'string', 'max:200'],
        ];
    }
}
