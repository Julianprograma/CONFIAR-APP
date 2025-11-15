
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios | Confiar App</title>
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
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        h1 { font-size: 32px; margin-bottom: 30px; }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }
        .filters {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        input, select {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
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
        .alert-error {
            background: rgba(239, 68, 68, .15);
            border: 1px solid rgba(239, 68, 68, .3);
            color: var(--danger);
        }
        .actions {
            display: flex;
            gap: 8px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">Confiar App</div>
            <nav>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.users.list') }}" style="color: var(--primary);">Usuarios</a>
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
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <h1>Gestión de Usuarios</h1>

        <div class="card">
            <form method="GET" class="filters">
                <input type="text" name="search" placeholder="Buscar por nombre o email..." value="{{ request('search') }}" style="flex:1;min-width:250px;">
                <select name="role">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <select name="status">
                    <option value="">Todos los estados</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                </select>
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('admin.users.list') }}" class="btn" style="background:var(--border);">Limpiar</a>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.update-role', $user) }}" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <select name="role_id" onchange="this.form.submit()" style="padding:6px 10px;font-size:13px;">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="actions">
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm" style="background:var(--warning);">
                                            {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">
                                No se encontraron usuarios
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:20px;">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</body>
</html>