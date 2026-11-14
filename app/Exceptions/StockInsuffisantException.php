<?php

namespace App\Exceptions;

use App\Models\Intrant;
use DomainException;

class StockInsuffisantException extends DomainException
{
    public function __construct(public readonly Intrant $intrant, float $disponible, float $demande)
    {
        parent::__construct(sprintf(
            'Stock insuffisant pour « %s » : %s %s disponible(s), %s demandé(s).',
            $intrant->nom, $disponible, $intrant->unite_mesure, $demande
        ));
    }
}
