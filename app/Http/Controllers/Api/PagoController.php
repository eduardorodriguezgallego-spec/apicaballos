<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Pago;
use App\Models\Reserva;
use App\Mail\PagoConfirmadoMail;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['reserva.usuario', 'reserva.caballo'])
            ->whereHas('reserva', function ($query) {
                $query->where('usuario_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pagos);
    }

    public function adminIndex()
    {
        $pagos = Pago::with(['reserva.usuario', 'reserva.caballo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pagos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'reserva_id' => 'required|exists:reservas,id',
            'plataforma' => 'required|string',
            'cantidad' => 'required|numeric',
            'comision' => 'required|numeric',
            'referencia_pago' => 'required|string'
        ]);

        $reserva = Reserva::where('usuario_id', auth()->id())
            ->find($request->reserva_id);

        if (!$reserva) {
            return response()->json([
                'mensaje' => 'Reserva no encontrada'
            ], 404);
        }

        return DB::transaction(function () use ($request, $reserva) {
            $reserva->refresh();

            if ($reserva->estado === 'cancelada') {
                return response()->json([
                    'mensaje' => 'No se puede pagar una reserva cancelada'
                ], 400);
            }

            if ($reserva->estado_pago === 'pagado') {
                return response()->json([
                    'mensaje' => 'Esta reserva ya fue pagada'
                ], 400);
            }

            $pago = Pago::create([
                'reserva_id' => $request->reserva_id,
                'plataforma' => $request->plataforma,
                'cantidad' => $request->cantidad,
                'comision' => $request->comision,
                'referencia_pago' => $request->referencia_pago,
                'estado' => 'pagado'
            ]);

            $reserva->update([
                'estado_pago' => 'pagado',
                'estado' => 'confirmada'
            ]);

            $reserva->load(['usuario', 'caballo', 'pago']);

            Mail::to($reserva->usuario->email)
                ->send(new PagoConfirmadoMail($pago, $reserva));

            return response()->json([
                'mensaje' => 'Pago registrado correctamente',
                'pago' => $pago,
                'reserva' => $reserva
            ]);
        });
    }
}