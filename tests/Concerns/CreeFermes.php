<?php

namespace Tests\Concerns;

use App\Models\Exploitation;
use App\Models\User;

trait CreeFermes
{
    /** Crée une exploitation avec un admin, un responsable et un agent. */
    protected function ferme(): array
    {
        $e = Exploitation::factory()->create();

        return [
            'e' => $e,
            'admin' => User::factory()->create(['exploitation_id' => $e->id, 'role' => 'admin']),
            'responsable' => User::factory()->create(['exploitation_id' => $e->id, 'role' => 'responsable']),
            'agent' => User::factory()->create(['exploitation_id' => $e->id, 'role' => 'agent']),
        ];
    }
}
