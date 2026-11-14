<?php

namespace App\Http\Requests;

use App\Models\Pesee;

class StorePeseeRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Pesee::class);
    }

    public function rules(): array
    {
        return [
            'date_pesee' => ['required', 'date', 'before_or_equal:today'],
            'poids_moyen_kg' => ['required', 'numeric', 'gt:0', 'max:400'],
            'effectif_pese' => ['required', 'integer', 'min:1'],
        ];
    }
}
