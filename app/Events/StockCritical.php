<?php

namespace App\Events;

use App\Models\Intrant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockCritical
{
    use Dispatchable, SerializesModels;

    public function __construct(public Intrant $intrant) {}
}
