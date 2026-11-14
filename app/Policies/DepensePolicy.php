<?php

namespace App\Policies;

/** Données financières : invisibles pour les agents. */
class DepensePolicy extends ExploitationPolicy
{
    protected array $lecture = ['admin', 'responsable'];
}
