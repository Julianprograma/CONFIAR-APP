<!DOCTYPE html>
<html>
<head>
    <title>Crear Nuevo Usuario</title>
</head>
<body>
    <h1>Crear Residente/Administrador</h1>
    
    @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div style="color: red;">
            // Muestra los errores de validación de Laravel
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf {{-- ¡CRÍTICO! Genera el token CSRF --}}
        
        <label for="name">Nombre:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="password_confirmation">Confirmar Contraseña:</label><br>
        <input type="password" id="password_confirmation" name="password_confirmation" required><br><br>

        <label for="role_id">Rol:</label><br>
        <select id="role_id" name="role_id" required>
            <option value="">Seleccionar Rol</option>
            {{-- Asumimos que los IDs 2 (Admin) y 3 (Residente) se pasan --}}
            @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select><br><br>
        
        <label for="apartment_id">Apartamento (Solo para Residentes):</label><br>
        <select id="apartment_id" name="apartment_id">
            <option value="">Ninguno / Seleccionar Apto</option>
            {{-- $apartments debe venir del UserController, solo aptos sin dueño --}}
            @foreach ($apartments as $apartment)
                <option value="{{ $apartment->id }}">{{ $apartment->code }}</option>
            @endforeach
        </select><br><br>

        <button type="submit">Crear Usuario</button>
    </form>
    
    <p><a href="{{ route('admin.dashboard') }}">Volver al Dashboard</a></p>
</body>
</html>