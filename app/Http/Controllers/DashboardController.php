<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboard)
    {
        return view('dashboard', [
            'stats' => $dashboard->stats(),
            'recentes' => Activite::with(['campagne', 'user'])->latest('date_activite')->latest('id')->limit(5)->get(),
        ]);
    }
}
