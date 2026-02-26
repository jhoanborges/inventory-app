<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RutaResource;
use App\Models\Ruta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RutaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $rutas = Ruta::with('operador')
            ->when($request->estado, fn ($q, $estado) => $q->where('estado', $estado))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return RutaResource::collection($rutas);
    }

    public function store(Request $request): RutaResource
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'origen' => 'required|string|max:255',
            'destino' => 'required|string|max:255',
            'operador_id' => 'nullable|exists:users,id',
            'vehiculo' => 'nullable|string|max:255',
            'estado' => 'nullable|string|in:pendiente,en_progreso,completada',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $ruta = Ruta::create($validated);

        return new RutaResource($ruta->load('operador'));
    }

    public function show(Ruta $ruta): RutaResource
    {
        return new RutaResource($ruta->load('operador'));
    }

    public function update(Request $request, Ruta $ruta): RutaResource
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'origen' => 'sometimes|string|max:255',
            'destino' => 'sometimes|string|max:255',
            'operador_id' => 'nullable|exists:users,id',
            'vehiculo' => 'nullable|string|max:255',
            'estado' => 'nullable|string|in:pendiente,en_progreso,completada',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $ruta->update($validated);

        return new RutaResource($ruta->load('operador'));
    }

    public function destroy(Ruta $ruta): JsonResponse
    {
        $ruta->delete();

        return response()->json(['message' => 'Ruta eliminada']);
    }
}
