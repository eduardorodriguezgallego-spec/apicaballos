<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ReservaCreadaMail extends Mailable
{
    public $reserva;

    public function __construct($reserva)
    {
        $this->reserva = $reserva;
    }

    public function build()
    {
        return $this->subject('Reserva confirmada')
            ->view('emails.reserva');
    }
}
