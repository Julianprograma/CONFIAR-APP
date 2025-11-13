<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pqrs;
use Illuminate\Support\Facades\Auth;

class PqrsController extends Controller
{
    // [RESIDENTE] Muestra el formulario para crear un PQRS
    public function create()
    {
        return view('resident.pqrs.create');
    }

    // [RESIDENTE] Almacena un nuevo PQRS
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:Queja,Reclamo,Sugerencia',
            'details' => 'required|string|min:10',
        ]);

        Pqrs::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'details' => $request->details,
            'status' => 'Abierto', // Estado inicial
        ]);

        return redirect()->route('resident.pqrs.my_list')->with('success', 'Su solicitud ha sido registrada con éxito.');
    }
    
    // [RESIDENTE] Muestra las PQRS propias
    public function showMyList()
    {
        $myPqrs = Pqrs::where('user_id', Auth::id())
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
                      
        return view('resident.pqrs.my_list', compact('myPqrs'));
    }

    // [ADMIN/SUPER USER] Lista todas las PQRS para gestión
    public function index()
    {
        $allPqrs = Pqrs::with('user')->orderBy('status', 'asc')->paginate(15);
        return view('admin.pqrs.index', compact('allPqrs'));
    }

    // [ADMIN/SUPER USER] Actualiza el estado del PQRS (e.g., de Abierto a Cerrado)
    public function updateStatus(Request $request, Pqrs $pqrs)
    {
        $request->validate([
            'status' => 'required|in:Abierto,Proceso,Cerrado',
        ]);
        
        $pqrs->update(['status' => $request->status]);

        return back()->with('success', 'El estado de la solicitud #' . $pqrs->id . ' ha sido actualizado.');
    }
}