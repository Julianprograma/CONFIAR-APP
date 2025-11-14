<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // En una aplicación Laravel con vistas Blade
        return view('auth.login'); 
    }

    /**
     * Procesa las credenciales de inicio de sesión.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. Validar los datos de la solicitud
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Intentar autenticar al usuario
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // 3. Redirigir según el rol del usuario (Lógica de redirección simple)
            $user = Auth::user();
            
            if ($user->role->name == 'Super Usuario' || $user->role->name == 'Administrador') {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            // Redirección por defecto para Residentes
            return redirect()->intended(route('resident.home')); 
        }

        // 4. Fallo de autenticación
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redirigir a la página de inicio o login
    }

    /**
     * Muestra el formulario de registro.
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Procesa el registro de un nuevo usuario.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:150','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'Residente', // ajusta según tu modelo
        ]);

        auth()->login($user);
        return redirect()->route('resident.home')->with('status','Cuenta creada correctamente.');
    }
}