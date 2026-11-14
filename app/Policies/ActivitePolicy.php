<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivitePolicy extends ExploitationPolicy
{
    protected array $ecriture = ['admin', 'responsable', 'agent'];

    /** Un agent ne modifie / supprime que ses propres activités. */
    public function update(User $user, Model $model): bool
    {
        if (! $this->memeExploitation($user, $model)) {
            return false;
        }

        return $user->aRole('admin', 'responsable') || $model->user_id === $user->id;
    }
}
