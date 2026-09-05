<?php

namespace Modules\Rosca\Listeners;

use Modules\Rosca\Events\WinnerSelected;
use Modules\Rosca\Notifications\WinnerNotification;
use Illuminate\Support\Facades\Notification;

class SendWinnerNotification
{
    public function handle(WinnerSelected $event)
    {
        if (! config('rosca.notify_on_win', true)) {
            return;
        }

        $member = $event->member;

        if ($member->user_id) {
            $userModel = config('auth.providers.users.model', \App\Models\User::class);

            try {
                $user = $userModel::find($member->user_id);

                if ($user && $user->email) {
                    Notification::send($user, new WinnerNotification($event->round));
                }
            } catch (\Throwable $e) {
                // swallow - best effort notification
            }
        }
    }
}
