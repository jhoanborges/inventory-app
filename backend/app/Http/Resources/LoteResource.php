<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto_id' => $this->producto_id,
            'numero_lote' => $this->numero_lote,
            'cantidad' => $this->cantidad,
            'fecha_fabricacion' => $this->fecha_fabricacion?->toDateString(),
            'fecha_vencimiento' => $this->fecha_vencimiento?->toDateString(),
            'estado' => $this->estado,
            'producto' => new ProductoResource($this->whenLoaded('producto')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
