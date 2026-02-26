<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto_id' => $this->producto_id,
            'lote_id' => $this->lote_id,
            'ruta_id' => $this->ruta_id,
            'user_id' => $this->user_id,
            'tipo' => $this->tipo,
            'cantidad' => $this->cantidad,
            'motivo' => $this->motivo,
            'producto' => new ProductoResource($this->whenLoaded('producto')),
            'lote' => new LoteResource($this->whenLoaded('lote')),
            'ruta' => new RutaResource($this->whenLoaded('ruta')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
