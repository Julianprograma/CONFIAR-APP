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
Route::middleware(['auth', 'role:Administrador,Super Usuario'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.list');
        Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::put('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

Route::middleware(['auth', 'role:Residente'])
    ->prefix('resident')->name('resident.')->group(function () {
        Route::get('/dashboard', [ResidentReportController::class, 'index'])->name('home');
        Route::get('/account-status', [ResidentReportController::class, 'showMyAccountStatus'])->name('account_status');
    });