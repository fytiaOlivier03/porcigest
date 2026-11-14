<?php

namespace App\Http\Requests;

use App\Models\Intrant;
use Illuminate\Validation\Rule;

class StoreIntrantRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Intrant::class);
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:120',
                Rule::unique('intrants', 'nom')->where('exploitation_id', $this->user()->exploitation_id)],
            'categorie' => ['required', Rule::in(Intrant::CATEGORIES)],
            'unite_mesure' => ['required', 'string', 'max:20'],
            'seuil_minimum' => ['required', 'numeric', 'min:0'],
            'cout_unitaire' => ['required', 'numeric', 'min:0'],
            'quantite_initiale' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
