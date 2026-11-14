<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampagneResource;
use App\Models\Campagne;
use App\Services\ZootechnieService;

class CampagneController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Campagne::class);

        return CampagneResource::collection(Campagne::with('unite')->latest('date_debut')->paginate(15));
    }

    public function show(Campagne $campagne, ZootechnieService $zoo)
    {
        $this->authorize('view', $campagne);

        return (new CampagneResource($campagne->load('unite')))->additional(['indicateurs' => $zoo->indicateurs($campagne)]);
    }
}
