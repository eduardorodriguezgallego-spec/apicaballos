<h1>Reserva confirmada</h1>

<p>Hola {{ $reserva->usuario->nombre }}</p>

<p>Tu reserva fue creada correctamente.</p>

<ul>
    <li>Caballo: {{ $reserva->caballo->nombre }}</li>
    <li>Fecha: {{ $reserva->fecha }}</li>
    <li>Hora: {{ $reserva->hora }}</li>
</ul>