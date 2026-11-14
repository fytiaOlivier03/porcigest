<?php

namespace App\Http\Requests;

use App\Models\Campagne;
use App\Models\Unite;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCampagneRequest extends TenantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Campagne::class);
    }

    public function rules(): array
    {
        return [
            'unite_id' => ['required', $this->existeChezMoi('unites')],
            'nom' => ['required', 'string', 'max:120'],
            'production' => ['required', 'string', 'max:120'],
            'date_debut' => ['required', 'date'],
            'date_fin_prevue' => ['required', 'date', 'after:date_debut'],
            'effectif_initial' => ['required', 'integer', 'min:1'],
            'statut' => ['required', Rule::in(['planifiee', 'en_cours'])],
        ];
    }

    /** Règles métier : capacité de l'unité et une seule campagne en cours par unité. */
    public function after(): array
    {
        return [function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $unite = Unite::find($this->unite_id);

            if ($this->integer('effectif_initial') > $unite->capacite) {
                $v->errors()->add('effectif_initial', "L'effectif dépasse la capacité de l'unité ({$unite->capacite}).");
            }
            if ($this->statut === 'en_cours' && Campagne::where('unite_id', $unite->id)->where('statut', 'en_cours')->exists()) {
                $v->errors()->add('unite_id', 'Cette unité a déjà une campagne en cours.');
            }
        }];
    }
}
