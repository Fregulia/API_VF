<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TreinadorCollection;
use App\Http\Resources\TreinadorResource;
use App\Models\Treinador;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use Exception;

class TreinadorController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new TreinadorCollection(Treinador::with(['esporte', 'atletas'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'cref' => 'required|string|max:50|unique:treinadors,cref',
                'especialidade' => 'nullable|string|max:255',
                'esporte_id' => 'nullable|exists:esportes,id',
                'atletas' => 'nullable|array',
                'atletas.*' => 'exists:atletas,id'
            ]);
            
            $treinador = Treinador::create($validated);
            if ($request->has('atletas')) {
                $treinador->atletas()->sync($request->atletas);
            }
            return new TreinadorResource($treinador->load(['esporte', 'atletas']));
        } catch (Exception $error) {
            $httpStatus = 500;
            if($error instanceof ValidationException) $httpStatus = 422;
            return response()->json(['error' => $error->getMessage()], $httpStatus);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Treinador $treinador)
    {
        return new TreinadorResource($treinador->load(['esporte', 'atletas']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Treinador $treinador)
    {
        try {
            $validated = $request->validate([
                'nome' => 'sometimes|required|string|max:255',
                'cref' => 'sometimes|required|string|max:50|unique:treinadors,cref,'.$treinador->id,
                'especialidade' => 'sometimes|nullable|string|max:255',
                'esporte_id' => 'sometimes|nullable|exists:esportes,id',
                'atletas' => 'sometimes|nullable|array',
                'atletas.*' => 'exists:atletas,id'
            ]);
            
            $treinador->update($validated);
            if ($request->has('atletas')) {
                $treinador->atletas()->sync($request->atletas);
            }
            return new TreinadorResource($treinador->load(['esporte', 'atletas']));
        } catch (Exception $error) {
            $httpStatus = 500;
            if($error instanceof ValidationException) $httpStatus = 422;
            return response()->json(['error' => $error->getMessage()], $httpStatus);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Treinador $treinador)
    {
        try {
            $treinador->delete();
            return response()->json(['message' => 'Treinador excluído com sucesso'], 200);
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
}
