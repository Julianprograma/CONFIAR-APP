<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommonArea;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // [RESIDENTE] Muestra el listado de zonas comunes disponibles para reservar
    public function index()
    {
        $areas = CommonArea::all();
        return view('resident.reservations.index', compact('areas'));
    }

    // [RESIDENTE] Procesa el intento de reserva
    public function store(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:common_areas,id',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);

        // 1. Verificar Conflictos de Horario
        $conflict = Reservation::where('area_id', $request->area_id)
                               ->where('status', 'Aprobada')
                               ->where(function($query) use ($request) {
                                   $query->whereBetween('start_datetime', [$request->start_datetime, $request->end_datetime])
                                         ->orWhereBetween('end_datetime', [$request->start_datetime, $request->end_datetime])
                                         ->orWhere(function($q) use ($request) {
                                             // Caso de que la reserva existente envuelva la nueva
                                             $q->where('start_datetime', '<=', $request->start_datetime)
                                               ->where('end_datetime', '>=', $request->end_datetime);
                                         });
                               })->exists();

        if ($conflict) {
            return back()->with('error', 'La zona ya está reservada en ese horario. Por favor, seleccione otro.');
        }

        // 2. Crear la Reserva (generalmente inicia como Pendiente de Aprobación del Admin)
        Reservation::create([
            'user_id' => Auth::id(),
            'area_id' => $request->area_id,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'status' => 'Pendiente', 
        ]);

        return back()->with('success', 'Reserva enviada para aprobación. Recibirá una notificación pronto.');
    }
    
    // [ADMIN/SUPER USER] Muestra listado de reservas pendientes de aprobación
    public function pendingApprovals()
    {
        $pending = Reservation::where('status', 'Pendiente')->with('user', 'area')->get();
        return view('admin.reservations.pending', compact('pending'));
    }
    
    // [ADMIN/SUPER USER] Aprueba una reserva y registra un posible cobro (opcional)
    public function approve(Reservation $reservation)
    {
        // Lógica de aprobación...
        $reservation->update(['status' => 'Aprobada']);
        
        // Aquí iría la lógica para generar una CUOTA por el valor de la reserva si es pagada.
        // E.g.: $this->createReservationDue($reservation);
        
        return back()->with('success', 'Reserva aprobada y notificada al residente.');
    }
}