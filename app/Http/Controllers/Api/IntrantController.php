<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IntrantResource;
use App\Models\Intrant;

class IntrantController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Intrant::class);

        return IntrantResource::collection(Intrant::orderBy('nom')->paginate(15));
    }

    public function show(Intrant $intrant)
    {
        $this->authorize('view', $intrant);

        return new IntrantResource($intrant);
    }
}
