<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- AGREGADO: ¡CRÍTICO!

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Se mantiene la lógica: verifica si está autenticado y si el rol coincide
        if (!Auth::check() || !in_array(Auth::user()->role->name, $roles)) {
            abort(403, 'Acceso Denegado.');
        }
        return $next($request);
    }
}