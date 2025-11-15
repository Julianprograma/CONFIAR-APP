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
        // Validar email y password
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        // Auth::attempt usa getAuthPassword() => password_hash
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // CORRECCIÓN: Validar por role_id (1=Super Usuario, 2=Admin, 3=Residente)
            if (in_array($user->role_id, [1, 2])) {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            // Residente (role_id = 3)
            if ($user->role_id == 3) {
                return redirect()->intended(route('resident.home'));
            }

            // Fallback por si el role_id no coincide
            return redirect('/')->with('error', 'Rol no reconocido. Contacta al administrador.');
        }

        return back()->withErrors([
            'email' => 'Credenciales inválidas.',
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
        $data = $request->validate([
            'first_name' => ['required','string','max:100'],
            'last_name'  => ['required','string','max:100'],
            'email'      => ['required','email','max:100','unique:users,email'],
            'password'   => ['required','string','min:8','confirmed'],
        ]);

        // CORRECCIÓN: Buscar por nombre O crear con role_id = 3 directamente
        $residenteRole = Role::where('name', 'Residente')->first();
        
        if (!$residenteRole) {
            // Si no existe el rol por nombre, asignar directamente role_id = 3
            $user = User::create([
                'first_name'    => $data['first_name'],
                'last_name'     => $data['last_name'],
                'email'         => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'role_id'       => 3, // Residente
                'is_active'     => true,
            ]);
        } else {
            $user = User::create([
                'first_name'    => $data['first_name'],
                'last_name'     => $data['last_name'],
                'email'         => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'role_id'       => $residenteRole->id,
                'is_active'     => true,
            ]);
        }

        auth()->login($user);
        return redirect()->route('resident.home')->with('status','Cuenta creada correctamente.');
    }
}