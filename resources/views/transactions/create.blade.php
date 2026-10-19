<!DOCTYPE html>
<html>
<head>
    <title>New Transaction</title>
    <style>body{font-family:sans-serif;max-width:600px;margin:40px auto;padding:0 20px;}
    input,select,textarea{display:block;width:100%;padding:8px;margin:8px 0;box-sizing:border-box;}
    button{padding:10px 20px;background:#2563eb;color:#fff;border:none;cursor:pointer;}
    .errors{background:#fee;padding:10px;border-left:4px solid red;margin-bottom: 20px;}</style>
</head>
<body>
    <h1>New Transaction</h1>
    <a href="{{ route('transactions.index') }}">← Back to list</a>
    <hr>

    @if($errors->any())
        <div class="errors">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf
        <label>Account</label>
        <select name="account_id" required>
            @foreach($accounts as $acc)
                <option value="{{ $acc->id }}">
                    {{ $acc->account_number }} — {{ $acc->user->name }} (Bal: {{ $acc->balance }})
                </option>
            @endforeach
        </select>

        <label>Type</label>
        <select name="type" required>
            <option value="deposit">Deposit</option>
            <option value="withdrawal">Withdrawal</option>
        </select>

        <label>Amount</label>
        <input type="number" step="0.01" name="amount" min="1" required>

        <label>Notes (optional)</label>
        <textarea name="notes"></textarea>

        <button type="submit">Submit Transaction</button>
    </form>
</body>
</html>