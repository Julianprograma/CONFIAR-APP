<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\MonthlyDue; // Necesario para las estadísticas contables
use App\Models\InvoicePayable;

class AdminController extends Controller
{
    /**
     * Muestra el dashboard principal para Administradores/Super Usuarios.
     * @return \Illuminate\View\View
     */
    public function dashboard() // Este es el método que la ruta ahora llama
    {
        $user = Auth::user();
        
        // ** ARREGLO TEMPORAL PARA EL ERROR DE 'invoice_payables' **
        // Tu base de datos aún no tiene estas tablas. 
        // Las comentamos para que el dashboard (y la app) pueda cargar.
        
        // Total de cuotas pendientes (Cuentas por Cobrar)
        // $pendingDuesCount = MonthlyDue::where('status', 'Pendiente')->count();
        // $pendingDuesTotal = MonthlyDue::where('status', 'Pendiente')->sum('base_amount');
        
        // Facturas de proveedores pendientes (Cuentas por Pagar)
        // $pendingInvoicesCount = InvoicePayable::where('status', 'Pendiente')->count(); // <--- ESTA LÍNEA CAUSA EL ERROR
        // $pendingInvoicesTotal = InvoicePayable::where('status', 'Pendiente')->sum('amount');
        
        // --- 2. AÑADIR ESTA LÍNEA ---
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', 1)->count(); // <-- LÍNEA EXISTENTE
        $inactiveUsers = $totalUsers - $activeUsers; // <-- LÍNEA EXISTENTE
        
        // --- ARREGLANDO $recentUsers (Error actual) ---
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        
        // --- PREVINIENDO EL SIGUIENTE ERROR ($recentPqrs) ---
        // La vista 'admin.DashboardAdmin.blade.php' pide esta variable.
        // Como el modelo/tabla PQRS no existe, enviamos un array vacío.
        $recentPqrs = [];
        // -------------------------

        // Puedes agregar más lógica de roles aquí si es necesario
        $viewData = [
            'userName' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
            'role' => $user->role->name,

            // Valores temporales (0) para que la vista no falle
            'pendingDuesCount' => 0,
            'pendingDuesTotal' => 0,
            'pendingInvoicesCount' => 0,
            'pendingInvoicesTotal' => 0,
            'totalUsers' => $totalUsers, // <-- 3. AÑADIR LA VARIABLE AL ARRAY
            'activeUsers' => $activeUsers, // <-- LÍNEA EXISTENTE
            'inactiveUsers' => $inactiveUsers, // <-- LÍNEA EXISTENTE
            'recentUsers' => $recentUsers, // <-- AÑADIR ESTA LÍNEA
            'recentPqrs' => $recentPqrs     // <-- AÑADIR ESTA LÍNEA
            // ... otras métricas (caja, bancos, etc.)
        ];

        // CAMBIO: La vista se llama 'admin.DashboardAdmin', no 'admin.dashboard'
        return view('admin.DashboardAdmin', $viewData);
    }
    
    // ... otros métodos de administración (gestión de usuarios, etc.)
}