<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Apartment;
use App\Models\Due;
use App\Models\Pqrs;
use App\Models\Reservation;

class ReportController extends Controller
{
    /**
     * Muestra el índice de reportes disponibles.
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Genera el Estado de Resultados (Income Statement) para un periodo.
     * Muestra Ingresos vs. Gastos.
     */
    public function generateIncomeStatement(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // 1. Obtener Transacciones filtradas por periodo
        $transactions = Transaction::whereBetween('date', [$startDate, $endDate])
                                    ->with('account')
                                    ->get();

        // 2. Clasificar y Sumar los movimientos (Basado en el Type de la Cuenta Contable)
        $incomeAccounts = Account::where('type', 'Ingreso')->pluck('id');
        $expenseAccounts = Account::where('type', 'Gasto')->pluck('id');

        $totalIncome = $transactions->whereIn('account_id', $incomeAccounts)
                                    ->sum(function ($t) {
                                        // Sumar solo los ingresos (ej. pagos de cuotas, ingresos por reservas)
                                        return $t->amount > 0 ? $t->amount : 0;
                                    });

        $totalExpenses = $transactions->whereIn('account_id', $expenseAccounts)
                                     ->sum(function ($t) {
                                        // Sumar solo los egresos/gastos (ej. pagos a proveedores, compras internas)
                                        return $t->amount < 0 ? abs($t->amount) : $t->amount; // Usamos valor absoluto
                                     });

        $netIncome = $totalIncome - $totalExpenses;

        $reportData = [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netIncome' => $netIncome,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ];

        return view('admin.reports.income_statement', $reportData);
    }
    
    // Aquí iría generateBalanceSheet(), que sería más complejo al requerir el saldo final
    // de todas las cuentas de Activo, Pasivo y Patrimonio a una fecha específica.

    /**
     * Dashboard principal del residente
     */
    public function residentIndex()
    {
        $user = auth()->user();
        
        // Buscar apartamento del residente
        $apartment = Apartment::where('owner_id', $user->id)->first();
        
        // Estadísticas generales
        $stats = [
            'pending_dues' => 0,
            'total_debt' => 0,
            'active_pqrs' => 0,
            'upcoming_reservations' => 0,
        ];

        if ($apartment) {
            // Cuotas pendientes
            $stats['pending_dues'] = Due::where('apartment_id', $apartment->id)
                ->where('status', 'Pendiente')
                ->count();
            
            $stats['total_debt'] = Due::where('apartment_id', $apartment->id)
                ->where('status', 'Pendiente')
                ->sum('amount');
            
            // PQRS activos (si el modelo existe)
            if (class_exists('App\Models\Pqrs')) {
                $stats['active_pqrs'] = Pqrs::where('user_id', $user->id)
                    ->whereIn('status', ['Pendiente', 'En Proceso'])
                    ->count();
            }
            
            // Reservas futuras (si el modelo existe)
            if (class_exists('App\Models\Reservation')) {
                $stats['upcoming_reservations'] = Reservation::where('apartment_id', $apartment->id)
                    ->where('reservation_date', '>=', now())
                    ->where('status', 'Aprobada')
                    ->count();
            }
        }

        // Últimas cuotas (si el modelo existe)
        $recentDues = [];
        if ($apartment && class_exists('App\Models\Due')) {
            $recentDues = Due::where('apartment_id', $apartment->id)
                ->orderBy('due_date', 'desc')
                ->limit(5)
                ->get();
        }

        return view('resident.dashboard', compact('user', 'apartment', 'stats', 'recentDues'));
    }

    /**
     * Muestra el estado de cuenta del residente autenticado
     */
    public function showMyAccountStatus()
    {
        $user = auth()->user();
        $apartment = Apartment::where('owner_id', $user->id)->first();

        if (!$apartment) {
            return redirect()->route('resident.home')
                ->with('error', 'No tienes un apartamento asignado. Contacta al administrador.');
        }

        // Todas las cuotas del apartamento
        $dues = Due::where('apartment_id', $apartment->id)
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        $summary = [
            'total_pending' => Due::where('apartment_id', $apartment->id)
                ->where('status', 'Pendiente')
                ->sum('amount'),
            'total_paid' => Due::where('apartment_id', $apartment->id)
                ->where('status', 'Pagada')
                ->sum('amount'),
        ];

        return view('resident.account-status', compact('apartment', 'dues', 'summary'));
    }
}