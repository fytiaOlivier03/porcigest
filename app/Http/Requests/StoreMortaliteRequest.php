<?php

namespace App\Http\Requests;

use App\Models\Mortalite;

class StoreMortaliteRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Mortalite::class);
    }

    public function rules(): array
    {
        return [
            'date_mortalite' => ['required', 'date', 'before_or_equal:today'],
            'nombre' => ['required', 'integer', 'min:1'],
            'cause' => ['nullable', 'string', 'max:150'],
        ];
    }
}
