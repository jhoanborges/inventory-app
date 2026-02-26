<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;

class ScanController extends Controller
{
    public function scan(string $barcode): ProductoResource
    {
        $producto = Producto::where('barcode', $barcode)->with('lotes')->firstOrFail();

        return new ProductoResource($producto);
    }
}
