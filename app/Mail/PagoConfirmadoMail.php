<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PagoConfirmadoMail extends Mailable
{
    public $pago;
    public $reserva;

    public function __construct($pago, $reserva)
    {
        $this->pago = $pago;
        $this->reserva = $reserva;
    }

    public function build()
    {
        return $this->subject('Pago confirmado')
            ->view('emails.pago-confirmado');
    }
}
