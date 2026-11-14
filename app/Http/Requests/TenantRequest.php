<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class TenantRequest extends FormRequest
{
    /** Règle "exists" limitée à l'exploitation de l'utilisateur (empêche de référencer les données d'une autre exploitation). */
    protected function existeChezMoi(string $table): \Illuminate\Validation\Rules\Exists
    {
        return Rule::exists($table, 'id')->where('exploitation_id', $this->user()->exploitation_id);
    }
}
