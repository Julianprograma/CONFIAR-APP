<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta | Confiar App</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <!-- Estilos de la página de Login (para consistencia) -->
    <style>
        :root {
            --bg: linear-gradient(135deg,#101726 0%,#1c2f4d 55%,#234d6f 100%);
            --card-bg: rgba(255,255,255,.08);
            --blur: 18px;
            --radius: 22px;
            --accent: #2d81ff;
            --accent-hover: #1662d3;
            --danger-bg: #ff4d4d;
            --danger-text: #600;
            --font: system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;
        }
        * { box-sizing:border-box; }
        body {
            margin:0; font-family:var(--font); min-height:100dvh;
            display:flex; align-items:center; justify-content:center;
            background:#0d1622; background-image:var(--bg); color:#f5f7fa;
            padding: 2rem 0;
        }
        .grid {
            width:100%; max-width:1180px; display:grid;
            grid-template-columns:repeat(auto-fit,minmax(420px,1fr)); gap:52px;
            padding:34px 38px;
        }
        .brand {
            display:flex; flex-direction:column; justify-content:center; gap:26px;
            padding:10px 4px;
        }
        .brand h1 {
            margin:0; font-size: clamp(42px,7vw,64px); line-height:1.05;
            background:linear-gradient(90deg,#fff,#d9e9ff,#9cc9ff);
            -webkit-background-clip:text; color:transparent;
            font-weight:700; letter-spacing:-1px;
        }
        .brand p {
            margin:0; max-width:520px; font-size:18px; opacity:.82; line-height:1.4;
        }
        .card {
            position:relative; backdrop-filter:blur(var(--blur));
            background:var(--card-bg); border:1px solid rgba(255,255,255,.20);
            border-radius:var(--radius); padding:42px 42px 38px; overflow:hidden;
            box-shadow:0 8px 28px -6px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.08) inset;
        }
        .card:before {
            content:""; position:absolute; inset:0;
            background:radial-gradient(circle at 78% 12%,rgba(255,255,255,.18),transparent 60%);
            pointer-events:none;
        }
        .logo {
            width:62px; height:62px; border-radius:16px;
            background:linear-gradient(135deg,#2d81ff,#6fb3ff);
            display:flex; align-items:center; justify-content:center;
            font-weight:600; font-size:24px; letter-spacing:.5px; color:#fff;
            box-shadow:0 8px 18px -6px rgba(45,129,255,.55);
            margin-bottom:22px;
        }
        h2 {
            margin:0 0 6px; font-size:26px; font-weight:600; letter-spacing:.5px;
        }
        .subtitle { margin:0 0 26px; font-size:14px; opacity:.75; line-height:1.4; }
        form { display:flex; flex-direction:column; gap:18px; }
        label { display:block; font-size:12px; font-weight:600; letter-spacing:.7px; text-transform:uppercase; margin-bottom:6px; opacity:.75;}
        .field { position:relative; }
        .field input {
            width:100%; padding:14px 16px;
            border:1px solid rgba(255,255,255,.28);
            background:rgba(255,255,255,.12);
            color:#fff; font-size:15px; border-radius:14px;
            outline:none; transition:.25s;
        }
        .field input:focus {
            border-color:var(--accent); box-shadow:0 0 0 3px rgba(45,129,255,.35);
            background:rgba(255,255,255,.18);
        }
        .icon {
            position:absolute; top:50%; left:16px; transform:translateY(-50%);
            font-size:18px; opacity:.55; pointer-events:none;
        }
        .btn {
            cursor:pointer; border:none; padding:15px 20px;
            font-size:16px; font-weight:600; letter-spacing:.4px;
            border-radius:14px; background:var(--accent); color:#fff;
            display:flex; align-items:center; justify-content:center;
            transition:.25s; box-shadow:0 6px 18px -4px rgba(45,129,255,.55);
        }
        .btn:hover { background:var(--accent-hover); transform:translateY(-2px); }
        .btn:active { transform:translateY(0); }
        .status, .error {
            font-size:13px; padding:12px 14px; border-radius:12px; line-height:1.3;
            backdrop-filter:blur(12px);
        }
        .status { background:rgba(47,162,255,.18); border:1px solid rgba(47,162,255,.45); }
        .error { background:rgba(255,45,45,.22); border:1px solid rgba(255,45,45,.55); }
        .errors-stack > div + div { margin-top:4px; }
        .footer {
            margin-top:30px; font-size:11px; opacity:.45; text-align:center;
        }
        @media (max-width:860px){
            .grid { padding:42px 30px; }
            .brand { display:none; }
            .card { max-width:460px; width:100%; }
        }
        @media (prefers-color-scheme:light){
            body { background:#f2f6fb; background-image:var(--bg); }
            .card { background:rgba(255,255,255,.85); }
            .field input { background:#fff; color:#0d2136; }
            .field input:focus { background:#fff; }
            .status { color:#063e63; }
            .error { color:#5b0000; }
            label { color:#0d2136; }
        }
    </style>
</head>
<body>
    <div class="grid">
        <section class="brand">
            <h1>Confiar App</h1>
            <p>Panel moderno para la administración y residentes. Ingresa para gestionar usuarios, cuotas, reservas y más en un entorno seguro y centralizado.</p>
        </section>

        <div class="card">
            <div class="logo">CA</div>
            <h2>Crear cuenta</h2>
            <p class="subtitle">Ingresa tus datos para registrarte</p>

            @if(session('error'))
                <div class="error">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="error errors-stack">
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Nombre -->
                <div class="field">
                    <label for="first_name">Nombre</label>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus>
                </div>
                
                <!-- Apellido -->
                <div class="field">
                    <label for="last_name">Apellido</label>
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required>
                </div>

                <!-- Email -->
                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="tu@correo.com">
                </div>
                
                <!-- Contraseña -->
                <div class="field">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="••••••••">
                </div>

                <!-- Confirmar Contraseña -->
                <div class="field">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                </div>

                <button type="submit" class="btn">Registrar Cuenta</button>
            </form>
            
            <div class="footer"> 
                <small>¿Ya tienes cuenta? <a style="color:#9cc9ff;text-decoration:none;" href="{{ route('login') }}">Iniciar sesión</a></small><br>
                &copy; {{ date('Y') }} Confiar App.
            </div>
        </div>
    </div>
</body>
</html>