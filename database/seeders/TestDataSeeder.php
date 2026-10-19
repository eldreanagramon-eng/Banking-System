<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Alice Santos',   'email' => 'alice@example.com'],
            ['name' => 'Bob Reyes',      'email' => 'bob@example.com'],
        ];

        foreach ($users as $i => $u) {
            $user = User::create([
                'name'     => $u['name'],
                'email'    => $u['email'],
                'password' => Hash::make('password'),
            ]);

            Account::create([
                'user_id'         => $user->id,
                'account_number'  => 'ACC-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'balance'         => 1000.00,
                'minimum_balance' => 100.00,
            ]);
        }
    }
}