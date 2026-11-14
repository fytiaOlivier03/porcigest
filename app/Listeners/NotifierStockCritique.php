<?php

namespace App\Listeners;

use App\Events\StockCritical;
use App\Models\User;
use App\Notifications\StockCritiqueNotification;
use App\Support\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

/** Listener asynchrone (queue) : prévient admin et responsables de l'exploitation. */
class NotifierStockCritique implements ShouldQueue
{
    public function handle(StockCritical $event): void
    {
        $destinataires = User::where('exploitation_id', $event->intrant->exploitation_id)
            ->whereIn('role', ['admin', 'responsable'])
            ->where('actif', true)
            ->get();

        Notification::send($destinataires, new StockCritiqueNotification($event->intrant));

        Audit::warning('Stock critique', [
            'intrant' => $event->intrant->nom,
            'quantite' => $event->intrant->quantite_disponible,
            'seuil' => $event->intrant->seuil_minimum,
        ]);
    }
}
