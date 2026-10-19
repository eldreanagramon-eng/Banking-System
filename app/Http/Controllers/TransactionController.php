<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('account.user')
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = Account::with('user')->get();
        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'type'       => ['required', 'in:deposit,withdrawal'],
            'amount'     => ['required', 'numeric', 'min:1'],
            'notes'      => ['nullable', 'string'],
        ]);

        // Custom pre-check for funds (friendly error before DB work)
        if ($validated['type'] === 'withdrawal') {
            $account = Account::findOrFail($validated['account_id']);
            if ($account->balance - $validated['amount'] < $account->minimum_balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient funds to complete this withdrawal.',
                ]);
            }
        }

        try {
            $transaction = DB::transaction(function () use ($validated) {
                return Transaction::create([
                    'account_id'       => $validated['account_id'],
                    'type'             => $validated['type'],
                    'amount'           => $validated['amount'],
                    'reference_number' => 'TXN-' . strtoupper(uniqid()),
                    'notes'            => $validated['notes'] ?? null,
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction recorded: ' . $transaction->reference_number);
    }
}