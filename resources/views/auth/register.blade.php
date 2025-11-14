
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro | Confiar App</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="color-scheme" content="light dark">
<style>
body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,sans-serif;min-height:100dvh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#101726,#1c2f4d 55%,#234d6f);}
.card{max-width:440px;width:100%;background:rgba(255,255,255,.10);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.25);padding:42px;border-radius:22px;color:#fff;}
h2{margin:0 0 10px;font-size:28px;font-weight:600;}
form{display:flex;flex-direction:column;gap:18px;}
label{font-size:12px;font-weight:600;letter-spacing:.7px;text-transform:uppercase;opacity:.75;margin-bottom:6px;}
input{width:100%;padding:14px 16px;border:1px solid rgba(255,255,255,.30);background:rgba(255,255,255,.14);color:#fff;font-size:15px;border-radius:14px;outline:none;transition:.25s;}
input:focus{border-color:#2d81ff;box-shadow:0 0 0 3px rgba(45,129,255,.35);background:rgba(255,255,255,.20);}
button{cursor:pointer;border:none;padding:15px 20px;font-size:16px;font-weight:600;border-radius:14px;background:#2d81ff;color:#fff;box-shadow:0 6px 18px -4px rgba(45,129,255,.55);transition:.25s;}
button:hover{background:#1662d3;transform:translateY(-2px);}
.error, .status{font-size:13px;padding:12px 14px;border-radius:12px;line-height:1.3;margin-bottom:14px;}
.error{background:rgba(255,45,45,.22);border:1px solid rgba(255,45,45,.55);}
.status{background:rgba(47,162,255,.18);border:1px solid rgba(47,162,255,.45);}
a{color:#9cc9ff;text-decoration:none;font-size:13px;}
a:hover{text-decoration:underline;}
</style>
</head>
<body>
    <div class="card">
        <h2>Crear cuenta</h2>
        <p style="opacity:.75;font-size:14px;margin-top:-4px;">Regístrate para acceder al panel de residente.</p>

        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="error">
                @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div>
                <label for="first_name">Nombre</label>
                <input type="text" id="first_name" name="first_name" required maxlength="100" value="{{ old('first_name') }}" placeholder="Tu nombre">
            </div>
            <div>
                <label for="last_name">Apellido</label>
                <input type="text" id="last_name" name="last_name" required maxlength="100" value="{{ old('last_name') }}" placeholder="Tu apellido">
            </div>
            <div>
                <label for="email">Correo</label>
                <input type="email" id="email" name="email" required autocomplete="email" value="{{ old('email') }}" placeholder="tu@correo.com">
            </div>
            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required minlength="8" placeholder="••••••••">
            </div>
            <div>
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Repite la contraseña">
            </div>
            <button type="submit">Registrarme</button>
        </form>

        <p style="margin-top:22px;text-align:center;">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
    </div>
</body>
</html>