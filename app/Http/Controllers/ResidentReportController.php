<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\MonthlyDue;
use Illuminate\Support\Facades\Auth;

class ResidentReportController extends Controller
{
    /**
     * Muestra el estado de cuenta del apartamento del usuario logueado (Residente).
     */
    public function showMyAccountStatus()
    {
        $user = Auth::user();

        if (!$user->apartment_id) {
            return redirect()->route('resident.home')->with('error', 'Su usuario no está asociado a un apartamento.');
        }

        $apartment = Apartment::findOrFail($user->apartment_id);

        // Obtener todas las cuotas (Cuentas por Cobrar) relacionadas con este apartamento
        $duesHistory = MonthlyDue::where('apartment_id', $apartment->id)
                                  ->orderBy('due_date', 'asc')
                                  ->get();

        // Calcular el saldo actual
        $balance = $duesHistory->sum(function ($due) {
            // Asumiendo: MontoBase si está pendiente/vencida, 0 si está pagada.
            return ($due->status == 'Pagada') ? 0 : $due->base_amount;
        });

        // NOTA: Para un cálculo de saldo preciso, deberías usar un campo 'balance' 
        // en la tabla 'apartments' o consultar transacciones específicas.

        $reportData = [
            'apartment' => $apartment,
            'ownerName' => $user->name,
            'duesHistory' => $duesHistory,
            'currentBalance' => $balance,
        ];

        return view('resident.account_status', $reportData);
    }

    /**
     * [ADMIN/SUPER USER] Muestra el estado de cuenta para un apartamento específico.
     */
    public function showApartmentAccountStatus(Apartment $apartment)
    {
        $duesHistory = MonthlyDue::where('apartment_id', $apartment->id)
                                  ->orderBy('due_date', 'asc')
                                  ->get();

        $balance = $duesHistory->sum(function ($due) {
            return ($due->status == 'Pagada') ? 0 : $due->base_amount;
        });
        
        $reportData = [
            'apartment' => $apartment,
            'ownerName' => $apartment->user->name ?? 'No Asignado',
            'duesHistory' => $duesHistory,
            'currentBalance' => $balance,
        ];

        return view('admin.reports.apartment_status', $reportData);
    }
}