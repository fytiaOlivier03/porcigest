<?php

namespace App\Http\Requests;

use App\Models\Recette;
use Illuminate\Validation\Rule;

class StoreRecetteRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Recette::class);
    }

    public function rules(): array
    {
        return [
            'campagne_id' => ['nullable', $this->existeChezMoi('campagnes')],
            'categorie' => ['required', Rule::in(Recette::CATEGORIES)],
            'libelle' => ['required', 'string', 'max:150'],
            'montant' => ['required', 'numeric', 'gt:0', 'max:100000000000'],
            'date_recette' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
