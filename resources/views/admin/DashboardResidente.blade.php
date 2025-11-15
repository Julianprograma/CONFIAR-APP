
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta | Confiar App</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root {
            --primary: #2d81ff;
            --bg: #0f172a;
            --card-bg: #1e293b;
            --border: #334155;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 28px; margin-bottom: 20px; }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }
        .summary-label { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; }
        .summary-value { font-size: 28px; font-weight: 700; }
        .summary-value.danger { color: var(--danger); }
        .summary-value.success { color: var(--success); }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border); }
        th { font-size: 12px; text-transform: uppercase; color: var(--text-muted); font-weight: 600; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: rgba(16, 185, 129, .2); color: var(--success); }
        .badge-danger { background: rgba(239, 68, 68, .2); color: var(--danger); }
        .badge-warning { background: rgba(245, 158, 11, .2); color: #f59e0b; }
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            background: var(--primary);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('resident.home') }}" class="btn" style="margin-bottom:20px;">← Volver</a>
        
        <h1>Estado de Cuenta - Apartamento {{ $apartment->number }}</h1>

        <div class="summary">
            <div class="summary-card">
                <div class="summary-label">SALDO PENDIENTE</div>
                <div class="summary-value danger">${{ number_format($summary['total_pending'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">TOTAL PAGADO</div>
                <div class="summary-value success">${{ number_format($summary['total_paid'], 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-bottom:20px;">Historial de Cuotas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th>Fecha Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dues as $due)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($due->period)->format('m/Y') }}</td>
                        <td>{{ $due->concept ?? 'Cuota mensual' }}</td>
                        <td>${{ number_format($due->amount, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($due->due_date)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $due->status == 'Pagada' ? 'badge-success' : ($due->status == 'Vencida' ? 'badge-danger' : 'badge-warning') }}">
                                {{ $due->status }}
                            </span>
                        </td>
                        <td>{{ $due->payment_date ? \Carbon\Carbon::parse($due->payment_date)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;">Sin cuotas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div style="margin-top:20px;">{{ $dues->links() }}</div>
        </div>
    </div>
</body>
</html>