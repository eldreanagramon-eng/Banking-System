<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:0 20px;}
    .stat{background:#f4f4f4;padding:15px;margin:10px 0;border-radius:6px;}
    .stat b{font-size:1.4em;color:#2563eb;}</style>
</head>
<body>
    <h1>📊 Real-Time Dashboard</h1>
    <a href="{{ route('transactions.index') }}">← Transactions</a>

    <div class="stat">Total Accounts: <b id="total-accounts">—</b></div>
    <div class="stat">Total Money in Bank: <b id="total-money">—</b></div>
    <div class="stat">Transactions Today: <b id="transactions-today">—</b></div>
    <p><small>Last updated: <span id="last-updated">never</span></small></p>

    <script>
        async function refreshStats() {
            try {
                const res = await fetch('/api/dashboard/stats', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();

                document.getElementById('total-accounts').textContent     = data.total_accounts;
                document.getElementById('total-money').textContent        =
                    Number(data.total_money).toLocaleString(undefined, { minimumFractionDigits: 2 });
                document.getElementById('transactions-today').textContent = data.transactions_today;
                document.getElementById('last-updated').textContent       =
                    new Date(data.generated_at).toLocaleTimeString();
            } catch (err) {
                console.error('Stats refresh failed:', err);
            }
        }

        refreshStats();                    // immediate
        setInterval(refreshStats, 30000);  // every 30 seconds
    </script>
</body>
</html>