<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Apartment;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
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

            // 3. Redirigir según el rol del usuario
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

        return redirect('/');
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
     * Procesa el registro de un nuevo usuario Residente.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // 1. Validar los datos de la solicitud
        $data = $request->validate([
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'email' => ['required','email','max:100','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            // 'apartment_number' => ['nullable', 'string', 'max:10'], 
        ]);

        // 2. Obtener el ID del rol 'Residente'
        $residenteRole = Role::where('name', 'Residente')->first();

        if (!$residenteRole) {
            return back()->with('error', 'Error de configuración: El rol Residente no existe en el sistema. Contacte al administrador.')->withInput();
        }

        // 3. Crear el usuario
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $residenteRole->id,
            'is_active' => true,
        ]);

        // 4. Iniciar sesión y redirigir
        auth()->login($user);
        return redirect()->route('resident.home')->with('status','Cuenta creada correctamente.');
    }
}