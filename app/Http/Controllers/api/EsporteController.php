<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EsporteCollection;
use App\Http\Resources\EsporteResource;
use App\Models\Esporte;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class EsporteController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new EsporteCollection(Esporte::with(['atletas', 'treinadores'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'federacao' => 'required|string|max:255',
                'descricao' => 'nullable|string'
            ]);
            
            $esporte = Esporte::create($validated);
            return new EsporteResource($esporte);
        } catch (Exception $error) {
            $httpStatus = 500;
            if($error instanceof ValidationException) $httpStatus = 422;
            return response()->json(['error' => $error->getMessage()], $httpStatus);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Esporte $esporte)
    {
        return new EsporteResource($esporte->load(['atletas', 'treinadores']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Esporte $esporte)
    {
        try {
            $validated = $request->validate([
                'nome' => 'sometimes|required|string|max:255',
                'federacao' => 'sometimes|required|string|max:255',
            ]);
            
            $esporte->update($validated);
            return new EsporteResource($esporte);
        } catch (Exception $error) {
            $httpStatus = 500;
            if($error instanceof ValidationException) $httpStatus = 422;
            return response()->json(['error' => $error->getMessage()], $httpStatus);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Esporte $esporte)
    {
        try {
            $esporte->delete();
            return response()->json(['message' => 'Esporte excluído com sucesso'], 200);
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
}
