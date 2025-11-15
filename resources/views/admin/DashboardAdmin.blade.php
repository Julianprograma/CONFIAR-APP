<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Confiar App</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root {
            --primary: #2d81ff;
            --primary-dark: #1662d3;
            --bg: #0f172a;
            --card-bg: #1e293b;
            --border: #334155;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 20px 0;
            margin-bottom: 30px;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 24px; font-weight: 700; color: var(--primary); }
        nav { display: flex; gap: 25px; align-items: center; }
        nav a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: .2s;
        }
        nav a:hover { color: var(--primary); }
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
        }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger {
            background: var(--danger);
            color: #fff;
        }
        h1 { font-size: 32px; margin-bottom: 10px; }
        .subtitle { color: var(--text-muted); font-size: 15px; margin-bottom: 30px; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }
        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary);
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }
        .card h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        th {
            font-size: 12px;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: .5px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: rgba(16, 185, 129, .2);
            color: var(--success);
        }
        .badge-danger {
            background: rgba(239, 68, 68, .2);
            color: var(--danger);
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: rgba(16, 185, 129, .15);
            border: 1px solid rgba(16, 185, 129, .3);
            color: var(--success);
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">Confiar App</div>
            <nav>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.users.list') }}">Usuarios</a>
                <span style="color: var(--text-muted);">{{ auth()->user()->first_name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Salir</button>
                </form>
            </nav>
        </div>
    </header>

    <div class="container">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <h1>Dashboard Administrativo</h1>
        <p class="subtitle">Bienvenido, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total Usuarios</div>
                <div class="stat-value">{{ $totalUsers }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Usuarios Activos</div>
                <div class="stat-value" style="color: var(--success);">{{ $activeUsers }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Usuarios Inactivos</div>
                <div class="stat-value" style="color: var(--danger);">{{ $inactiveUsers }}</div>
            </div>
        </div>

        <div class="card">
            <h2>Usuarios Recientes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                        <tr>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role->name }}</td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;color:var(--text-muted);">No hay usuarios registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div style="margin-top:20px;">
                <a href="{{ route('admin.users.list') }}" class="btn btn-primary">Ver todos los usuarios</a>
            </div>
        </div>
    </div>
</body>
</html>