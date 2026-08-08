<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;


class NotifySubscriptionExpiration extends Command
{
    protected $signature = 'subscription:notify-expiring';

    protected $description = "Notifie les clients dont l'abonnement va expirer ou a expiré.";

    public function handle(NotificationService $notifications): int
    {
        $today = Carbon::today()->startOfDay();
        $targets = [7, 3, 1, 0];

        $subscriptions = Subscription::query()
            ->whereNotNull('date_fin')
            ->whereHas('user', function ($q) {
                $q->whereRaw('LOWER(role) = ?', ['client']);
            })
            ->get();

        $notified = 0;

        foreach ($subscriptions as $subscription) {
            $daysLeft = (int) $subscription->date_fin->startOfDay()->diffInDays($today, false);

            // Seulement les abonnements encore considérés comme actifs jusqu'à l'expiration.
            if (! in_array($daysLeft, $targets, true)) {
                continue;
            }

            $client = $subscription->user;
            if (! $client instanceof User) {
                continue;
            }

            $label = match ($daysLeft) {
                0 => 'expire aujourd\'hui',
                1 => 'expire demain',
                3 => 'expire dans 3 jours',
                7 => 'expire dans 7 jours',
                default => 'va bientôt expirer',
            };

            $title = $daysLeft <= 0
                ? 'Abonnement expiré'
                : 'Abonnement bientôt expiré';

            $message = "Votre abonnement EduManager {$label} (le {$subscription->date_fin->format('d/m/Y')}). "
                . 'Veuillez renouveler pour éviter le blocage de votre établissement.';

            $notifications->sendToUsers(
                $client,
                collect([$client]),
                $title,
                $message,
                'subscription',
                $daysLeft <= 0 ? 'high' : 'normal'
            );

            $notified++;
        }

        $this->info("Notifications d'expiration envoyées : {$notified}.");

        return self::SUCCESS;
    }
}
