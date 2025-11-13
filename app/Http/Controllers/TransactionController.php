<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Apartment;

class TransactionController extends Controller
{
    /**
     * Muestra el formulario para registrar una nueva transacción (Ingreso o Egreso).
     */
    public function create()
    {
        $accounts = Account::all();
        $apartments = Apartment::all(); // Necesario para registrar pagos de cuotas

        return view('admin.transactions.create', compact('accounts', 'apartments'));
    }

    /**
     * Almacena una nueva transacción manual (para compras internas o ingresos no relacionados con cuotas/facturas).
     * Este es un registro de asiento simple: Cuenta afectada (Crédito/Débito) vs Caja/Banco.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:Ingreso,Egreso',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            // Otras validaciones opcionales: supplier_id, apartment_id, etc.
        ]);

        // La Cuenta de Contrapartida (Caja o Bancos) tendrá el ID 1 o 2 en tu tabla 'accounts'
        // Asumiremos que la cuenta de BANCOS tiene ID=2 para esta prueba.
        $cash_bank_account_id = 2; 

        // Usamos una transacción de DB para asegurar que ambos registros se guarden o ninguno.
        DB::beginTransaction();

        try {
            // 1. Registro de la Cuenta Afectada (Gasto, Ingreso, Activo...)
            $transaction = Transaction::create([
                'account_id' => $request->account_id,
                'description' => $request->description,
                'date' => $request->date,
                // El signo depende del tipo de movimiento contable
                // Los Gastos/Egresos son un débito. Los Ingresos son un crédito.
                'amount' => ($request->type == 'Egreso') ? -$request->amount : $request->amount,
            ]);

            // 2. Registro de la Contrapartida (Caja/Banco)
            Transaction::create([
                'account_id' => $cash_bank_account_id,
                'description' => 'Contrapartida por: ' . $request->description,
                'date' => $request->date,
                // Si es Ingreso, la Caja/Banco aumenta (Crédito o débito si usamos contabilidad estandar)
                // Para simplificar la tabla 'transactions' y usar un solo campo 'amount':
                // Si es Egreso, la Caja/Banco disminuye (-amount)
                // Si es Ingreso, la Caja/Banco aumenta (+amount)
                'amount' => ($request->type == 'Egreso') ? -$request->amount : $request->amount,
                'related_entity_id' => $transaction->id,
                'related_type' => 'ManualTransaction'
            ]);

            DB::commit();

            return redirect()->route('admin.transactions.index')->with('success', 'Transacción registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la transacción: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la lista de transacciones.
     */
    public function index()
    {
        $transactions = Transaction::with('account')->orderBy('date', 'desc')->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }
}