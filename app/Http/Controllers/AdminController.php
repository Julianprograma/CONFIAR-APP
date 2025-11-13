<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MonthlyDue; // Necesario para las estadísticas contables
use App\Models\InvoicePayable;

class AdminController extends Controller
{
    /**
     * Muestra el dashboard principal para Administradores/Super Usuarios.
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // **ESTADÍSTICAS RÁPIDAS (Pruebas Contables)**
        
        // Total de cuotas pendientes (Cuentas por Cobrar)
        $pendingDuesCount = MonthlyDue::where('status', 'Pendiente')->count();
        $pendingDuesTotal = MonthlyDue::where('status', 'Pendiente')->sum('base_amount');
        
        // Facturas de proveedores pendientes (Cuentas por Pagar)
        $pendingInvoicesCount = InvoicePayable::where('status', 'Pendiente')->count();
        $pendingInvoicesTotal = InvoicePayable::where('status', 'Pendiente')->sum('amount');

        // Puedes agregar más lógica de roles aquí si es necesario
        $viewData = [
            'userName' => $user->name,
            'role' => $user->role->name,
            'pendingDuesCount' => $pendingDuesCount,
            'pendingDuesTotal' => $pendingDuesTotal,
            'pendingInvoicesCount' => $pendingInvoicesCount,
            'pendingInvoicesTotal' => $pendingInvoicesTotal,
            // ... otras métricas (caja, bancos, etc.)
        ];

        return view('admin.dashboard', $viewData);
    }
    
    // ... otros métodos de administración (gestión de usuarios, etc.)
}