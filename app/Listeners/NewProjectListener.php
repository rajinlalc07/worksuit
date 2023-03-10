<?php

namespace App\Listeners;

use App\Events\NewProjectEvent;
use App\Notifications\NewProject;
use App\Models\User;
use App\Scopes\ActiveScope;
use Illuminate\Support\Facades\Notification;

class NewProjectListener
{

    /**
     * @param NewProjectEvent $event
     */

    public function handle(NewProjectEvent $event)
    {
        if (($event->project->client_id != null)) {
            $clientId = $event->project->client_id;
            // Notify client
            $notifyUser = User::withoutGlobalScope(ActiveScope::class)->findOrFail($clientId);

            if ($notifyUser) {
                Notification::send($notifyUser, new NewProject($event->project));
            }
        }
    }

}
