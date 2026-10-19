<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>
    <style>body{font-family:sans-serif;max-width:800px;margin:40px auto;padding:0 20px;}
    table{width:100%;border-collapse:collapse;} th,td{border:1px solid #ccc;padding:8px;text-align:left;}
    .success{background:#efe;padding:10px;border-left:4px solid green;margin:10px 0;}</style>
</head>
<body>
    <h1>All Transactions</h1>
    <p>
        <a href="{{ route('transactions.create') }}">+ New Transaction</a> |
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </p>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <table>
        <thead>
            <tr><th>Ref #</th><th>Account</th><th>Type</th><th>Amount</th><th>Date</th></tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->reference_number }}</td>
                    <td>{{ $tx->account->account_number }} ({{ $tx->account->user->name }})</td>
                    <td>{{ ucfirst($tx->type) }}</td>
                    <td>{{ number_format($tx->amount, 2) }}</td>
                    <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No transactions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>