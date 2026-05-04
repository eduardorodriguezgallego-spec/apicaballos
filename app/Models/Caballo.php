<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caballo extends Model
{
    protected $table = 'caballos';

    protected $fillable = [
        'nombre',
        'raza',
        'fecha_nacimiento',
        'foto',
        'enfermo',
        'observaciones'
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }

        return asset('storage/' . $this->foto);
    }
}
