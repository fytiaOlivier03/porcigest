<?php

namespace App\Http\Requests;

use App\Models\Activite;
use Illuminate\Validation\Rule;

class StoreActiviteRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Activite::class);
    }

    public function rules(): array
    {
        return [
            'campagne_id' => ['required', $this->existeChezMoi('campagnes')],
            'type' => ['required', Rule::in(Activite::TYPES)],
            'date_activite' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cout' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
