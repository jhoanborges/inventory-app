<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoteResource;
use App\Models\Lote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $lotes = Lote::with('producto')
            ->when($request->producto_id, fn ($q, $id) => $q->where('producto_id', $id))
            ->when($request->estado, fn ($q, $estado) => $q->where('estado', $estado))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return LoteResource::collection($lotes);
    }

    public function store(Request $request): LoteResource
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'numero_lote' => 'required|string|unique:lotes',
            'cantidad' => 'required|integer|min:1',
            'fecha_fabricacion' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_fabricacion',
            'estado' => 'nullable|string|in:activo,vencido,agotado',
        ]);

        $lote = Lote::create($validated);

        return new LoteResource($lote->load('producto'));
    }

    public function show(Lote $lote): LoteResource
    {
        return new LoteResource($lote->load('producto'));
    }

    public function update(Request $request, Lote $lote): LoteResource
    {
        $validated = $request->validate([
            'producto_id' => 'sometimes|exists:productos,id',
            'numero_lote' => 'sometimes|string|unique:lotes,numero_lote,'.$lote->id,
            'cantidad' => 'sometimes|integer|min:0',
            'fecha_fabricacion' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'nullable|string|in:activo,vencido,agotado',
        ]);

        $lote->update($validated);

        return new LoteResource($lote->load('producto'));
    }

    public function destroy(Lote $lote): JsonResponse
    {
        $lote->delete();

        return response()->json(['message' => 'Lote eliminado']);
    }
}
