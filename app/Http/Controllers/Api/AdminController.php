<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Caballo;
use App\Models\Reserva;
use App\Models\Pago;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS GENERALES
    |--------------------------------------------------------------------------
    */

    public function estadisticas()
    {
        return response()->json([

            'total_usuarios' => User::count(),

            'total_caballos' => Caballo::count(),

            'caballos_enfermos' => Caballo::where('enfermo', true)->count(),

            'total_reservas' => Reserva::count(),

            'reservas_pendientes_pago' => Reserva::where('estado_pago', 'pendiente')->count(),

            'reservas_pagadas' => Reserva::where('estado_pago', 'pagado')->count(),

            'total_pagos' => Pago::count(),

            'dinero_total' => Pago::where('estado', 'pagado')->sum('cantidad'),

            'comisiones_total' => Pago::where('estado', 'pagado')->sum('comision'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        return response()->json([

            'usuarios' => [
                'total' => User::count(),
            ],

            'caballos' => [
                'total' => Caballo::count(),
                'enfermos' => Caballo::where('enfermo', true)->count(),
                'disponibles' => Caballo::where('enfermo', false)->count(),
            ],

            'reservas' => [
                'total' => Reserva::count(),
                'pendientes' => Reserva::where('estado', 'pendiente')->count(),
                'confirmadas' => Reserva::where('estado', 'confirmada')->count(),
                'canceladas' => Reserva::where('estado', 'cancelada')->count(),
            ],

            'pagos' => [
                'total' => Pago::count(),
                'dinero_total' => Pago::sum('cantidad'),
                'comisiones' => Pago::sum('comision'),
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LISTADO DE USUARIOS
    |--------------------------------------------------------------------------
    */

    public function usuarios()
    {
        $usuarios = User::orderBy('created_at', 'desc')->get();

        return response()->json($usuarios);
    }

    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO RESERVA
    |--------------------------------------------------------------------------
    */

    public function cambiarEstadoReserva(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,cancelada'
        ]);

        $reserva = Reserva::with([
            'usuario',
            'caballo',
            'pago'
        ])->find($id);

        if (!$reserva) {
            return response()->json([
                'mensaje' => 'Reserva no encontrada'
            ], 404);
        }

        $reserva->update([
            'estado' => $request->estado
        ]);

        return response()->json([
            'mensaje' => 'Estado de reserva actualizado correctamente',
            'reserva' => $reserva
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BLOQUEO AUTOMÁTICO DE RESERVAS SIN PAGAR (🔥 PRO+)
    |--------------------------------------------------------------------------
    */

    public function cancelarReservasPendientes()
    {
        $limite = Carbon::now()->subMinutes(30);

        $reservas = Reserva::where('estado_pago', 'pendiente')
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('created_at', '<=', $limite)
            ->get();

        foreach ($reservas as $reserva) {
            $reserva->update([
                'estado' => 'cancelada'
            ]);
        }

        return response()->json([
            'mensaje' => 'Reservas pendientes canceladas automáticamente',
            'total_canceladas' => $reservas->count(),
            'reservas' => $reservas
        ]);
    }
}