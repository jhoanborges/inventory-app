<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $productos = Producto::with('lotes')
            ->when($request->search, fn ($q, $search) => $q->where('nombre', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%"))
            ->when($request->categoria, fn ($q, $cat) => $q->where('categoria', $cat))
            ->orderBy('nombre')
            ->paginate($request->per_page ?? 15);

        return ProductoResource::collection($productos);
    }

    public function store(Request $request): ProductoResource
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:productos',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:255',
            'unidad_medida' => 'required|string|max:50',
            'precio' => 'nullable|numeric|min:0',
            'stock_actual' => 'integer|min:0',
            'stock_minimo' => 'integer|min:0',
            'barcode' => 'nullable|string|unique:productos',
            'imagen' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $producto = Producto::create($validated);

        return new ProductoResource($producto);
    }

    public function show(Producto $producto): ProductoResource
    {
        return new ProductoResource($producto->load('lotes'));
    }

    public function update(Request $request, Producto $producto): ProductoResource
    {
        $validated = $request->validate([
            'sku' => 'sometimes|string|unique:productos,sku,'.$producto->id,
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:255',
            'unidad_medida' => 'sometimes|string|max:50',
            'precio' => 'nullable|numeric|min:0',
            'stock_actual' => 'integer|min:0',
            'stock_minimo' => 'integer|min:0',
            'barcode' => 'nullable|string|unique:productos,barcode,'.$producto->id,
            'imagen' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $producto->update($validated);

        return new ProductoResource($producto);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }

    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*.sku' => 'required|string',
            'productos.*.nombre' => 'required|string|max:255',
            'productos.*.unidad_medida' => 'required|string|max:50',
            'productos.*.precio' => 'nullable|numeric|min:0',
            'productos.*.stock_actual' => 'nullable|integer|min:0',
            'productos.*.stock_minimo' => 'nullable|integer|min:0',
            'productos.*.barcode' => 'nullable|string',
            'productos.*.categoria' => 'nullable|string|max:255',
        ]);

        $created = 0;
        $updated = 0;

        foreach ($request->productos as $data) {
            $producto = Producto::updateOrCreate(
                ['sku' => $data['sku']],
                $data
            );

            $producto->wasRecentlyCreated ? $created++ : $updated++;
        }

        return response()->json([
            'message' => "Sincronización completada: {$created} creados, {$updated} actualizados",
            'created' => $created,
            'updated' => $updated,
        ]);
    }
}
