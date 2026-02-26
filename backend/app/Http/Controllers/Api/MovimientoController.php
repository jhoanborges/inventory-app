<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovimientoResource;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $movimientos = MovimientoInventario::with(['producto', 'lote', 'ruta', 'user'])
            ->when($request->producto_id, fn ($q, $id) => $q->where('producto_id', $id))
            ->when($request->tipo, fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return MovimientoResource::collection($movimientos);
    }

    public function store(Request $request): MovimientoResource
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'lote_id' => 'nullable|exists:lotes,id',
            'ruta_id' => 'nullable|exists:rutas,id',
            'tipo' => 'required|string|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = $request->user()->id;

        $movimiento = DB::transaction(function () use ($validated) {
            $movimiento = MovimientoInventario::create($validated);

            $producto = Producto::findOrFail($validated['producto_id']);

            if ($validated['tipo'] === 'entrada') {
                $producto->increment('stock_actual', $validated['cantidad']);
            } else {
                $producto->decrement('stock_actual', $validated['cantidad']);
            }

            return $movimiento;
        });

        return new MovimientoResource($movimiento->load(['producto', 'lote', 'ruta', 'user']));
    }
}
