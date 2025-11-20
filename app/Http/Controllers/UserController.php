<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Apartment;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios (Residentes y Administradores).
     * Accessible por: Administrador, Super Usuario.
     */
    public function index()
    {
        // Excluye al Super Usuario (role_id = 1) de la lista general
        $users = User::with('role', 'apartment')->where('role_id', '!=', 1)->paginate(15);
        $roles = Role::where('name', '!=', 'Super Usuario')->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario (Residente o Administrador).
     */
    public function create()
    {
        $roles = Role::where('name', '!=', 'Super Usuario')->get();
        $apartments = Apartment::doesntHave('user')->get(); // Solo apartamentos sin residente asignado

        return view('admin.users.create', compact('roles', 'apartments'));
    }

    /**
     * Almacena un nuevo usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'apartment_id' => 'nullable|exists:apartments,id',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            // Solo se asigna apartment_id si el rol es 'Residente' (asumiendo role_id=3)
            'apartment_id' => ($request->role_id == 3) ? $request->apartment_id : null, 
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario ' . $user->name . ' creado exitosamente.');
    }
    
    /**
     * [SUPER USUARIO] Permite cambiar el rol de un Administrador.
     * @param \App\Models\User $user El usuario a delegar/modificar.
     */
    public function delegateRole(Request $request, User $user)
    {
        // Solo Super Usuario puede acceder a esta ruta (gestionado por Middleware)
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        
        // Evitar modificar al Super Usuario (role_id=1) o a sí mismo
        if ($user->role_id === 1 || $user->id === auth()->id()) {
            return back()->with('error', 'No puedes modificar el rol del Super Usuario o el tuyo propio.');
        }

        $user->update(['role_id' => $request->role_id]);
        
        return back()->with('success', "Rol de {$user->name} actualizado a {$user->role->name}.");
    }

    //Actualización de roles

    public function updateRole(Request $request, User $user)
    {
        // 1. Autorización: Solo el Super Usuario puede ejecutar esta acción.
        // Usamos la constante si la definiste, o el ID 1.
        if (Auth::user()->role_id !== Role::SUPER_USUARIO) {
            return back()->with('error', 'Acceso denegado. Solo un Super Usuario tiene este privilegio.');
        }

        // 2. Validación de la Solicitud
        $request->validate([
            // Asegura que el nuevo role_id sea 2 (Administrador) o 3 (Residente)
            'role_id' => 'required|integer|in:' . Role::ADMINISTRADOR . ',' . Role::RESIDENTE,
        ]);
        
        $newRoleId = $request->role_id;
        
        // 3. Restricción adicional (Opcional, pero recomendado):
        // Bloquear intentos de cambiar el rol del Super Usuario
        if ($user->role_id === Role::SUPER_USUARIO && $newRoleId !== Role::SUPER_USUARIO) {
            return back()->with('error', 'No puedes cambiar el rol del Super Usuario.');
        }
        
        // 4. Ejecutar la Actualización
        $user->role_id = $newRoleId;
        $user->save();

        // 5. Preparar mensaje de respuesta
        $roleName = Role::find($newRoleId)->name ?? 'Rol Desconocido';
        
        return back()->with('status', "El rol de {$user->first_name} ha sido actualizado a {$roleName}.");
    }

    // Nota: Aquí irían los métodos update() y destroy().
}