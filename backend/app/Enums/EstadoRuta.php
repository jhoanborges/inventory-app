<?php

namespace App\Enums;

enum EstadoRuta: string
{
    case Pendiente = 'pendiente';
    case EnProgreso = 'en_progreso';
    case Completada = 'completada';
}
