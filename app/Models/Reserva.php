<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'usuario_id',
        'caballo_id',
        'fecha',
        'hora',
        'comentarios',
        'estado',
        'tipo_pago',
        'estado_pago',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function caballo()
    {
        return $this->belongsTo(Caballo::class, 'caballo_id');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'reserva_id');
    }
}
