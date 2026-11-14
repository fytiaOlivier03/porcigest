<?php

namespace App\Policies;

/** Saisie terrain : ouverte aussi aux agents. */
class PeseePolicy extends ExploitationPolicy
{
    protected array $ecriture = ['admin', 'responsable', 'agent'];
}
