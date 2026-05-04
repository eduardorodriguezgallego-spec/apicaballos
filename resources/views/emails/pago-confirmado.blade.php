<h1>Pago confirmado</h1>

<p>Hola {{ $reserva->usuario->nombre }},</p>

<p>Tu pago se ha registrado correctamente.</p>

<ul>
    <li>Caballo: {{ $reserva->caballo->nombre }}</li>
    <li>Fecha: {{ $reserva->fecha }}</li>
    <li>Hora: {{ $reserva->hora }}</li>
    <li>Cantidad: {{ $pago->cantidad }} €</li>
    <li>Referencia: {{ $pago->referencia_pago }}</li>
</ul>

<p>Tu reserva queda confirmada.</p>