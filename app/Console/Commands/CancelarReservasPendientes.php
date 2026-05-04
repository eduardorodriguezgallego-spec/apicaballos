<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use Carbon\Carbon;

class CancelarReservasPendientes extends Command
{
    protected $signature = 'reservas:cancelar-pendientes';

    protected $description = 'Cancela reservas pendientes de pago con más de 30 minutos';

    public function handle()
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

        $this->info('Reservas canceladas: ' . $reservas->count());

        return Command::SUCCESS;
    }
}