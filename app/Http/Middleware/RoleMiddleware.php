<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        
        if (!$user) {
            abort(403, 'No autenticado.');
        }

        // Si el usuario no tiene role_id asignado
        if (!$user->role_id) {
            abort(403, 'Usuario sin rol asignado.');
        }

        // Convertir nombres de rol a IDs para comparar
        $roleIds = [];
        foreach ($roles as $roleName) {
            switch ($roleName) {
                case 'Super Usuario':
                    $roleIds[] = 1;
                    break;
                case 'Administrador':
                    $roleIds[] = 2;
                    break;
                case 'Residente':
                    $roleIds[] = 3;
                    break;
            }
        }

        // Verificar si el role_id del usuario está en los permitidos
        if (!in_array($user->role_id, $roleIds)) {
            abort(403, 'No autorizado para esta sección.');
        }

        return $next($request);
    }
}