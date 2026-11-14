<?php

namespace App\Listeners;

use App\Events\CampagneCompleted;
use App\Jobs\GenererRapportCampagne;

class LancerRapportCampagne
{
    public function handle(CampagneCompleted $event): void
    {
        GenererRapportCampagne::dispatch($event->campagne->id);
    }
}
