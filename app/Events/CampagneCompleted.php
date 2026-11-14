<?php

namespace App\Events;

use App\Models\Campagne;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampagneCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public Campagne $campagne) {}
}
