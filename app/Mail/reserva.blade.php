<h1>Reserva confirmada</h1>

<p>Hola {{ $reserva->usuario->nombre }}</p>

<p>Tu reserva ha sido creada correctamente.</p>

<ul>
    <li>Caballo: {{ $reserva->caballo->nombre }}</li>
    <li>Fecha: {{ $reserva->fecha }}</li>
    <li>Hora: {{ $reserva->hora }}</li>
</ul>

<p>Gracias por confiar en nosotros.</p>