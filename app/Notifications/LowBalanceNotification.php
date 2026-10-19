<?php

namespace App\Notifications;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class LowBalanceNotification extends Notification
{
    use Queueable;

    public function __construct(public Account $account) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'account_id'     => $this->account->id,
            'account_number' => $this->account->account_number,
            'balance'        => $this->account->balance,
            'minimum'        => $this->account->minimum_balance,
            'message'        => "Account {$this->account->account_number} has reached or fallen below its minimum balance.",
        ]);
    }
}