<?php

namespace Modules\Rosca\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Rosca\Models\Round;

class WinnerNotification extends Notification
{
    use Queueable;

    protected $round;

    public function __construct(Round $round)
    {
        $this->round = $round;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $rosca = $this->round->rosca;

        return (new MailMessage)
            ->subject('You won the Rosca round!')
            ->greeting('Congratulations!')
            ->line('You have been selected as the winner for round ' . $this->round->round_number . ' of ' . $rosca->name)
            ->line('Amount: ' . number_format($this->round->collected_amount, 2))
            ->line('Thank you for using our Chama/Rosca service.');
    }
}
