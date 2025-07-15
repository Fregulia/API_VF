<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AtletaCollection;
use App\Http\Resources\AtletaResource;
use App\Models\Atleta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Exception;

class AtletaController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new AtletaCollection(Atleta::with(['esporte', 'treinadores'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'idade' => 'nullable|integer|min:0',
                'categoria' => 'nullable|string|max:100',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'esporte_id' => 'nullable|exists:esportes,id',
                'treinadores' => 'nullable|array',
                'treinadores.*' => 'exists:treinadors,id'
            ]);
            
            if ($request->hasFile('foto')) {
                $validated['foto'] = $request->file('foto')->store('atletas', 'public');
            }
            
            $atleta = Atleta::create($validated);
            if ($request->has('treinadores')) {
                $atleta->treinadores()->sync($request->treinadores);
            }
            return new AtletaResource($atleta->load(['esporte', 'treinadores']));
        } catch (Exception $error) {
            $httpStatus = 500;
            if($error instanceof ValidationException) $httpStatus = 422;
            return response()->json(['error' => $error->getMessage()], $httpStatus);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Atleta $atleta)
    {
        return new AtletaResource($atleta->load(['esporte', 'treinadores']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atleta $atleta)
    {
        try {
            $validated = $request->validate([
                'nome' => 'sometimes|required|string|max:255',
                'idade' => 'sometimes|nullable|integer|min:0',
                'categoria' => 'sometimes|nullable|string|max:100',
                'foto' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
                'esporte_id' => 'sometimes|nullable|exists:esportes,id',
                'treinadores' => 'sometimes|nullable|array',
                'treinadores.*' => 'exists:treinadors,id'
            ]);
            
            if ($request->hasFile('foto')) {
                if ($atleta->foto) {
                    Storage::disk('public')->delete($atleta->foto);
                }
                $validated['foto'] = $request->file('foto')->store('atletas', 'public');
            }
            
            $atleta->update($validated);
            if ($request->has('treinadores')) {
                $atleta->treinadores()->sync($request->treinadores);
            }
            return new AtletaResource($atleta->load(['esporte', 'treinadores']));
        } catch (Exception $error) {
            $httpStatus = 500;
            if($error instanceof ValidationException) $httpStatus = 422;
            return response()->json(['error' => $error->getMessage()], $httpStatus);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atleta $atleta)
    {
        try {
            if ($atleta->foto) {
                Storage::disk('public')->delete($atleta->foto);
            }
            $atleta->delete();
            return response()->json(['message' => 'Atleta excluído com sucesso'], 200);
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
}
