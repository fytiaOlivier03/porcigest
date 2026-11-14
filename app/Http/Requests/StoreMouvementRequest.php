<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreMouvementRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('intrant'));
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['entree', 'ajustement'])],
            // entrée : strictement positive ; ajustement : signé et non nul
            'quantite' => array_merge(['required', 'numeric', 'between:-1000000,1000000', 'not_in:0'],
                $this->input('type') === 'entree' ? ['gt:0'] : []),
            'motif' => [Rule::requiredIf($this->input('type') === 'ajustement'), 'nullable', 'string', 'max:200'],
            'date_mouvement' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
