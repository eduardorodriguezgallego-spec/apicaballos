<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica que el usuario esté autenticado
        if (!$request->user()) {
            return response()->json([
                'mensaje' => 'Usuario no autenticado'
            ], 401);
        }

        // Verifica que el usuario sea administrador
        if ($request->user()->rol !== 'admin') {
            return response()->json([
                'mensaje' => 'No tienes permisos de administrador'
            ], 403);
        }

        return $next($request);
    }
}
