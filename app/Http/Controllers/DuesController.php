<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Apartment;
use App\Models\BillingPeriod;
use App\Models\MonthlyDue;
use App\Models\Transaction;

class DuesController extends Controller
{
    /**
     * Muestra la vista para generar o ver cuotas por periodo.
     * Accesible por: Administrador, Super Usuario.
     */
    public function index()
    {
        $periods = BillingPeriod::orderBy('id', 'desc')->get();
        return view('admin.dues.index', compact('periods'));
    }

    /**
     * [ADMIN/SUPER USER] Genera masivamente las cuotas para un nuevo periodo de facturación.
     * @param \Illuminate\Http\Request $request
     */
    public function generate(Request $request)
    {
        $request->validate([
            'period_name' => 'required|unique:billing_periods,period_name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'due_date' => 'required|date|after:start_date',
            'base_amount' => 'required|numeric|min:0', // Monto estándar de la cuota
        ]);

        DB::beginTransaction();
        try {
            // 1. Crear el Periodo de Facturación
            $period = BillingPeriod::create([
                'period_name' => $request->period_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            $apartments = Apartment::all();
            $dues = [];

            // 2. Generar una Cuota (MonthlyDue) para cada apartamento
            foreach ($apartments as $apartment) {
                $dues[] = [
                    'apartment_id' => $apartment->id,
                    'period_id' => $period->id,
                    'base_amount' => $request->base_amount,
                    'due_date' => $request->due_date,
                    'status' => 'Pendiente',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            MonthlyDue::insert($dues);
            DB::commit();

            return back()->with('success', 'Se han generado ' . count($dues) . ' cuotas para el periodo ' . $period->period_name . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar las cuotas: ' . $e->getMessage());
        }
    }

    /**
     * [ADMIN/SUPER USER] Marca una cuota como pagada y crea la transacción contable.
     * @param \App\Models\MonthlyDue $due
     */
    public function markAsPaid(MonthlyDue $due)
    {
        if ($due->status === 'Pagada') {
            return back()->with('error', 'Esta cuota ya ha sido pagada.');
        }

        DB::beginTransaction();
        try {
            // 1. Actualizar el estado de la cuota
            $due->update([
                'status' => 'Pagada',
                'payment_date' => Carbon::now(),
            ]);

            // 2. Crear la Transacción Contable (Ingreso)
            // Asumiremos que la cuenta de INGRESOS POR CUOTAS tiene ID=3 y BANCOS tiene ID=2
            $income_account_id = 3; 
            $cash_bank_account_id = 2;
            $amount = $due->base_amount;

            // Registro 1: Débito a Bancos (Aumento de Activo)
            Transaction::create([
                'account_id' => $cash_bank_account_id,
                'description' => 'Pago de cuota #' . $due->id . ' Apto ' . $due->apartment->code,
                'date' => Carbon::now(),
                'amount' => $amount, 
                'related_entity_id' => $due->id,
                'related_type' => 'MonthlyDue',
            ]);

            // Registro 2: Crédito a Ingresos por Cuotas (Aumento de Ingreso)
            Transaction::create([
                'account_id' => $income_account_id,
                'description' => 'Ingreso por Cuota ' . $due->period->period_name . ' Apto ' . $due->apartment->code,
                'date' => Carbon::now(),
                'amount' => $amount, // Usamos valor positivo para simplificar el reporte de ingresos
                'related_entity_id' => $due->id,
                'related_type' => 'MonthlyDue',
            ]);

            DB::commit();

            return back()->with('success', 'Cuota pagada y transacción contable registrada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }
}