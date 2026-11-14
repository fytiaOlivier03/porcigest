<?php

namespace App\Notifications;

use App\Models\Intrant;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Canaux : "database" (cloche / page Notifications de l'application, toujours disponible)
 *          "mail"     (e-mail ; en développement MAIL_MAILER=log écrit le message dans storage/logs).
 */
class StockCritiqueNotification extends Notification
{
    public function __construct(public Intrant $intrant) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Alerte stock critique : '.$this->intrant->nom)
            ->line($this->message())
            ->action('Voir le stock', url('/intrants/'.$this->intrant->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'intrant_id' => $this->intrant->id,
            'message' => $this->message(),
        ];
    }

    private function message(): string
    {
        return sprintf(
            'Alerte : le stock de « %s » (%s %s) est inférieur ou égal au seuil minimum (%s).',
            $this->intrant->nom, $this->intrant->quantite_disponible, $this->intrant->unite_mesure, $this->intrant->seuil_minimum
        );
    }
}
