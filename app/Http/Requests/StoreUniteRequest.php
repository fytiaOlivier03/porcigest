<?php

namespace App\Http\Requests;

use App\Models\Unite;
use Illuminate\Validation\Rule;

class StoreUniteRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Unite::class);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20',
                Rule::unique('unites', 'code')->where('exploitation_id', $this->user()->exploitation_id)],
            'nom' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(Unite::TYPES)],
            'capacite' => ['required', 'integer', 'min:1', 'max:100000'],
            'localisation' => ['nullable', 'string', 'max:150'],
        ];
    }
}
