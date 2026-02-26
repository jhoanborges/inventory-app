<?php

namespace App\Models;

use App\Enums\EstadoLote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lote extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'numero_lote',
        'cantidad',
        'fecha_fabricacion',
        'fecha_vencimiento',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoLote::class,
            'fecha_fabricacion' => 'date',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
