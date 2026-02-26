<?php

namespace App\Enums;

enum EstadoLote: string
{
    case Activo = 'activo';
    case Vencido = 'vencido';
    case Agotado = 'agotado';
}
