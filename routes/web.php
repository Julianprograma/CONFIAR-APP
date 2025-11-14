<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResidentReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DuesController;
use App\Http\Controllers\PqrsController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| Ruta Raíz
|-------------------------------------------------------------------------
*/
Route::get('/', function () { 
    return redirect()->route('login'); 
});

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


/*
|--------------------------------------------------------------------------
| Rutas Protegidas de Administradores y Super Usuarios (admin.)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Administrador,Super Usuario'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard principal para administradores
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Ruta de prueba para Super Usuario (más restrictiva)
    Route::middleware(['role:Super Usuario'])->get('/settings', function () {
        return view('admin.super_settings');
    })->name('super.settings');

    // --- Gestión de Usuarios (UserController) ---
    Route::resource('users', UserController::class)->except(['show']);
    
    // Ruta específica de delegación (Solo Super Usuario)
    Route::middleware(['role:Super Usuario'])->put('users/{user}/delegate', [UserController::class, 'delegateRole'])->name('users.delegate');

    // --- Gestión de Transacciones (TransactionController) ---
    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store']);

    // --- Gestión de Reportes Contables (ReportController y ResidentReportController) ---
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/income-statement', [ReportController::class, 'generateIncomeStatement'])->name('income_statement');
        
        // Estado de Cuenta de Apartamentos (para que el Admin consulte cualquier apto)
        Route::get('/account-status/{apartment}', [ResidentReportController::class, 'showApartmentAccountStatus'])->name('apartment_status');
    });

    // --- Gestión de Cuotas (DuesController) ---
    Route::prefix('dues')->name('dues.')->group(function () {
        Route::get('/', [DuesController::class, 'index'])->name('index');
        Route::post('/generate', [DuesController::class, 'generate'])->name('generate'); // Generación Masiva
        Route::put('/{due}/pay', [DuesController::class, 'markAsPaid'])->name('mark_paid'); // Registro de Pago
    });

    // --- Gestión de PQRS (PqrsController) ---
    Route::resource('pqrs', PqrsController::class)->only(['index']); // Listar/Ver PQRS para el admin
    Route::put('pqrs/{pqrs}/status', [PqrsController::class, 'updateStatus'])->name('pqrs.update_status');
    
    // --- Gestión de Reservas (ReservationController) ---
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/pending', [ReservationController::class, 'pendingApprovals'])->name('pending');
        Route::put('/{reservation}/approve', [ReservationController::class, 'approve'])->name('approve');
    });

}); // CIERRE CORRECTO del grupo admin.


/*
|--------------------------------------------------------------------------
| Rutas para Residentes (resident.)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Residente'])->prefix('resident')->name('resident.')->group(function () {
    
    // Panel básico para residentes
    Route::get('/home', function () {
        return view('resident.home');
    })->name('home');
    
    // --- Estado de Cuenta Individual (ResidentReportController) ---
    Route::get('/my-account-status', [ResidentReportController::class, 'showMyAccountStatus'])->name('account_status');

    // --- Módulo PQRS (Residente) ---
    Route::get('/pqrs/create', [PqrsController::class, 'create'])->name('pqrs.create');
    Route::post('/pqrs', [PqrsController::class, 'store'])->name('pqrs.store');
    Route::get('/pqrs/my-list', [PqrsController::class, 'showMyList'])->name('pqrs.my_list');

    // --- Módulo Reservas (Residente) ---
    Route::resource('reservations', ReservationController::class)->only(['index', 'store']);

});