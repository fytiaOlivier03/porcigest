<?php

namespace App\Policies;

/** Données financières : invisibles pour les agents. */
class RecettePolicy extends ExploitationPolicy
{
    protected array $lecture = ['admin', 'responsable'];
}
