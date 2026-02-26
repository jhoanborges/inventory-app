<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'unidad_medida' => $this->unidad_medida,
            'precio' => $this->precio,
            'stock_actual' => $this->stock_actual,
            'stock_minimo' => $this->stock_minimo,
            'barcode' => $this->barcode,
            'imagen' => $this->imagen,
            'activo' => $this->activo,
            'lotes' => LoteResource::collection($this->whenLoaded('lotes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
