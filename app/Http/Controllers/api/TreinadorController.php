<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TreinadorCollection;
use App\Http\Resources\TreinadorResource;
use App\Models\Treinador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'esporte_id' => 'nullable|exists:esportes,id',
                'atletas' => 'nullable|array',
                'atletas.*' => 'exists:atletas,id'
            ]);
            
            if ($request->hasFile('foto')) {
                $validated['foto'] = $request->file('foto')->store('treinadores', 'public');
            }
            
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
            // Log para debug
            \Log::info("Atualizando treinador ID: {$treinador->id}");
            \Log::info("Dados recebidos: " . json_encode($request->all()));
            \Log::info("Arquivos recebidos: " . json_encode($request->allFiles()));
            
            $validated = $request->validate([
                'nome' => 'sometimes|required|string|max:255',
                'cref' => 'sometimes|required|string|max:50|unique:treinadors,cref,'.$treinador->id,
                'especialidade' => 'sometimes|nullable|string|max:255',
                'foto' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'esporte_id' => 'sometimes|nullable|exists:esportes,id',
                'atletas' => 'sometimes|nullable|array',
                'atletas.*' => 'exists:atletas,id'
            ]);
            
            \Log::info("Dados validados: " . json_encode($validated));
            
            if ($request->hasFile('foto')) {
                \Log::info("Arquivo de foto recebido: " . $request->file('foto')->getClientOriginalName());
                
                if ($treinador->foto) {
                    \Log::info("Excluindo foto anterior: {$treinador->foto}");
                    Storage::disk('public')->delete($treinador->foto);
                }
                
                $path = $request->file('foto')->store('treinadores', 'public');
                \Log::info("Nova foto salva em: {$path}");
                $validated['foto'] = $path;
            }
            
            $treinador->update($validated);
            \Log::info("Treinador atualizado com sucesso: " . json_encode($treinador));
            
            if ($request->has('atletas')) {
                \Log::info("Sincronizando atletas: " . json_encode($request->atletas));
                $treinador->atletas()->sync($request->atletas);
            }
            
            // Recarregar o modelo para garantir que temos os dados mais recentes
            $treinador->refresh();
            \Log::info("Dados finais do treinador: " . json_encode($treinador));
            
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
            if ($treinador->foto) {
                Storage::disk('public')->delete($treinador->foto);
            }
            $treinador->delete();
            return response()->json(['message' => 'Treinador excluído com sucesso'], 200);
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
}
