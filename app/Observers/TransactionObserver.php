<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Lock the row to prevent race conditions
            $account = $transaction->account()->lockForUpdate()->first();

            $newBalance = $transaction->type === 'deposit'
                ? $account->balance + $transaction->amount
                : $account->balance - $transaction->amount;

            // CRITICAL RULE: withdrawal cannot drop below minimum_balance
            if ($transaction->type !== 'deposit'
                && $newBalance < $account->minimum_balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient funds: balance cannot fall below the minimum of '
                        . number_format($account->minimum_balance, 2) . '.',
                ]);
            }

            $account->update(['balance' => $newBalance]);

            // Low-balance notification (edge-triggered to avoid duplicates)
            if ($transaction->type !== 'deposit'
                && $newBalance <= $account->minimum_balance) {

                $recent = $account->user->notifications()
                    ->where('data->account_id', $account->id)
                    ->whereNull('read_at')
                    ->exists();

                if (! $recent) {
                    $account->user->notify(new LowBalanceNotification($account));
                }
            }
        });
    }
}