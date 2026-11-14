<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDistributionRequest;
use App\Http\Resources\DistributionResource;
use App\Services\StockService;
use DomainException;

class DistributionController extends Controller
{
    public function store(StoreDistributionRequest $request, StockService $stock)
    {
        try {
            $distribution = $stock->distribuerAliment($request->validated(), $request->user());
        } catch (DomainException $e) {   // stock insuffisant, campagne terminée...
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new DistributionResource($distribution->load('intrant')))
            ->response()->setStatusCode(201);
    }
}
