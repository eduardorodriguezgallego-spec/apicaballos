<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\ReservaCreadaMail;
use App\Models\Reserva;
use App\Models\Caballo;
use Carbon\Carbon;

class ReservaController extends Controller
{
    private array $horasPermitidas = ['10:00', '11:00', '12:00', '13:00'];
    private int $capacidadMaxima = 5;

    public function index()
    {
        $reservas = Reserva::with(['usuario', 'caballo', 'pago'])
            ->where('usuario_id', auth()->id())
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        return response()->json($reservas);
    }

    public function adminIndex()
    {
        $reservas = Reserva::with(['usuario', 'caballo', 'pago'])
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        return response()->json($reservas);
    }

    public function show($id)
    {
        $reserva = Reserva::with(['usuario', 'caballo', 'pago'])
            ->where('usuario_id', auth()->id())
            ->find($id);

        if (!$reserva) {
            return response()->json([
                'mensaje' => 'Reserva no encontrada'
            ], 404);
        }

        return response()->json($reserva);
    }

    public function store(Request $request)
    {
        $request->validate([
            'caballo_id' => 'required|exists:caballos,id',
            'fecha' => 'required|date',
            'hora' => 'required|in:' . implode(',', $this->horasPermitidas),
            'comentarios' => 'nullable|string'
        ]);

        $validacion = $this->validarReserva($request);

        if ($validacion !== true) {
            return $validacion;
        }

        $reserva = Reserva::create([
            'usuario_id' => auth()->id(),
            'caballo_id' => $request->caballo_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'comentarios' => $request->comentarios,
            'estado' => 'pendiente',
            'tipo_pago' => 'reserva_20',
            'estado_pago' => 'pendiente'
        ]);

        $reserva->load(['usuario', 'caballo']);

        $reserva->expira_en = $reserva->created_at->copy()->addMinutes(30);
        $reserva->minutos_restantes_pago = now()->diffInMinutes($reserva->expira_en, false);

        Mail::to(auth()->user()->email)
            ->send(new ReservaCreadaMail($reserva));

        $this->enviarWhatsappReserva($reserva);

        return response()->json([
            'mensaje' => 'Reserva creada correctamente',
            'reserva' => $reserva
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $reserva = Reserva::where('usuario_id', auth()->id())->find($id);

        if (!$reserva) {
            return response()->json([
                'mensaje' => 'Reserva no encontrada'
            ], 404);
        }

        $request->validate([
            'caballo_id' => 'required|exists:caballos,id',
            'fecha' => 'required|date',
            'hora' => 'required|in:' . implode(',', $this->horasPermitidas),
            'comentarios' => 'nullable|string'
        ]);

        $validacion = $this->validarReserva($request, $id);

        if ($validacion !== true) {
            return $validacion;
        }

        $reserva->update([
            'caballo_id' => $request->caballo_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'comentarios' => $request->comentarios,
        ]);

        $reserva->load(['usuario', 'caballo', 'pago']);

        return response()->json([
            'mensaje' => 'Reserva actualizada correctamente',
            'reserva' => $reserva
        ]);
    }

    public function destroy($id)
    {
        $reserva = Reserva::where('usuario_id', auth()->id())->find($id);

        if (!$reserva) {
            return response()->json([
                'mensaje' => 'Reserva no encontrada'
            ], 404);
        }

        $reserva->delete();

        return response()->json([
            'mensaje' => 'Reserva eliminada correctamente'
        ]);
    }

    public function disponibilidad(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|in:' . implode(',', $this->horasPermitidas)
        ]);

        $caballosReservados = Reserva::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->pluck('caballo_id');

        $caballosDisponibles = Caballo::where('enfermo', false)
            ->whereNotIn('id', $caballosReservados)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'caballos_disponibles' => $caballosDisponibles
        ]);
    }

    public function calendario(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date'
        ]);

        $calendario = [];

        foreach ($this->horasPermitidas as $hora) {
            $reservasTurno = Reserva::with(['usuario', 'caballo'])
                ->where('fecha', $request->fecha)
                ->where('hora', $hora)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->get();

            $caballosReservados = $reservasTurno->pluck('caballo_id');

            $caballosDisponibles = Caballo::where('enfermo', false)
                ->whereNotIn('id', $caballosReservados)
                ->orderBy('nombre')
                ->get();

            $ocupadas = $reservasTurno->count();

            $calendario[] = [
                'fecha' => $request->fecha,
                'hora' => $hora,
                'capacidad' => $this->capacidadMaxima,
                'ocupadas' => $ocupadas,
                'libres' => max($this->capacidadMaxima - $ocupadas, 0),
                'completo' => $ocupadas >= $this->capacidadMaxima,
                'caballos_disponibles' => $caballosDisponibles,
                'reservas' => $reservasTurno
            ];
        }

        return response()->json([
            'fecha' => $request->fecha,
            'calendario' => $calendario
        ]);
    }

    public function calendarioSemana(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date'
        ]);

        $inicio = Carbon::parse($request->fecha)->startOfWeek();
        $fin = $inicio->copy()->addDays(6);

        $semana = [];

        for ($dia = $inicio->copy(); $dia->lte($fin); $dia->addDay()) {
            $fecha = $dia->toDateString();
            $turnos = [];
            $diaCompleto = true;

            foreach ($this->horasPermitidas as $hora) {
                $reservasTurno = Reserva::with(['usuario', 'caballo'])
                    ->where('fecha', $fecha)
                    ->where('hora', $hora)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->get();

                $caballosReservados = $reservasTurno->pluck('caballo_id');

                $caballosDisponibles = Caballo::where('enfermo', false)
                    ->whereNotIn('id', $caballosReservados)
                    ->orderBy('nombre')
                    ->get();

                $ocupadas = $reservasTurno->count();
                $libres = max($this->capacidadMaxima - $ocupadas, 0);
                $completo = $ocupadas >= $this->capacidadMaxima;

                if (!$completo) {
                    $diaCompleto = false;
                }

                $turnos[] = [
                    'hora' => $hora,
                    'capacidad' => $this->capacidadMaxima,
                    'ocupadas' => $ocupadas,
                    'libres' => $libres,
                    'completo' => $completo,
                    'caballos_disponibles' => $caballosDisponibles,
                    'reservas' => $reservasTurno
                ];
            }

            $semana[] = [
                'fecha' => $fecha,
                'dia_semana' => $dia->locale('es')->isoFormat('dddd'),
                'reservable' => in_array($dia->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
                'completo' => $diaCompleto,
                'turnos' => $turnos
            ];
        }

        return response()->json([
            'inicio' => $inicio->toDateString(),
            'fin' => $fin->toDateString(),
            'semana' => $semana
        ]);
    }

    private function validarReserva(Request $request, $reservaId = null)
    {
        $fechaReserva = Carbon::parse($request->fecha);
        $hoy = Carbon::now()->startOfDay();

        if ($fechaReserva->lt($hoy)) {
            return response()->json([
                'mensaje' => 'No se puede reservar una fecha pasada'
            ], 400);
        }

        if ($fechaReserva->gt(Carbon::now()->addDays(30))) {
            return response()->json([
                'mensaje' => 'Solo se puede reservar con 30 días de antelación'
            ], 400);
        }

        if (!in_array($fechaReserva->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
            return response()->json([
                'mensaje' => 'Solo se puede reservar sábados y domingos'
            ], 400);
        }

        $caballo = Caballo::find($request->caballo_id);

        if (!$caballo) {
            return response()->json([
                'mensaje' => 'Caballo no encontrado'
            ], 404);
        }

        if ($caballo->enfermo) {
            return response()->json([
                'mensaje' => 'No se puede reservar un caballo enfermo'
            ], 400);
        }

        $totalTurno = Reserva::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->when($reservaId, fn($q) => $q->where('id', '!=', $reservaId))
            ->count();

        if ($totalTurno >= $this->capacidadMaxima) {
            return response()->json([
                'mensaje' => 'Turno completo'
            ], 400);
        }

        $caballoReservado = Reserva::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->where('caballo_id', $request->caballo_id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->when($reservaId, fn($q) => $q->where('id', '!=', $reservaId))
            ->exists();

        if ($caballoReservado) {
            return response()->json([
                'mensaje' => 'Ese caballo ya está reservado en ese turno'
            ], 400);
        }

        return true;
    }

    private function enviarWhatsappReserva(Reserva $reserva): void
    {
        if (!env('CALLMEBOT_PHONE') || !env('CALLMEBOT_APIKEY')) {
            return;
        }

        $mensaje =
            "🐎 Reserva creada correctamente\n" .
            "Fecha: {$reserva->fecha}\n" .
            "Hora: {$reserva->hora}\n" .
            "Caballo: {$reserva->caballo->nombre}";

        Http::get('https://api.callmebot.com/whatsapp.php', [
            'phone' => env('CALLMEBOT_PHONE'),
            'text' => $mensaje,
            'apikey' => env('CALLMEBOT_APIKEY')
        ]);
    }
}