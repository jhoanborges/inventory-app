<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RutaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'origen' => $this->origen,
            'destino' => $this->destino,
            'operador_id' => $this->operador_id,
            'vehiculo' => $this->vehiculo,
            'estado' => $this->estado,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'operador' => $this->whenLoaded('operador', fn () => [
                'id' => $this->operador->id,
                'name' => $this->operador->name,
                'email' => $this->operador->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
