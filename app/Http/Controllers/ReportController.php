<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use Carbon\Carbon;

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
}