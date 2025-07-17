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
    public function show($id)
    {
        try {
            // Log para debug
            \Log::info("Buscando atleta com ID: {$id}");
            
            // Verificar se o ID é válido
            if (!is_numeric($id)) {
                \Log::error("ID de atleta inválido: {$id}");
                return response()->json(['error' => 'ID de atleta inválido'], 400);
            }
            
            // Buscar o atleta explicitamente pelo ID
            $atleta = Atleta::where('id', $id)->first();
            
            // Verificar se o atleta foi encontrado
            if (!$atleta) {
                \Log::error("Atleta não encontrado com ID: {$id}");
                return response()->json(['error' => 'Atleta não encontrado'], 404);
            }
            
            // Carregar relacionamentos
            $atleta->load(['esporte', 'treinadores']);
            
            \Log::info("Atleta encontrado: " . json_encode($atleta));
            
            // Retornar o recurso com os dados formatados
            return new AtletaResource($atleta);
        } catch (\Exception $e) {
            \Log::error("Erro ao buscar atleta: " . $e->getMessage());
            return response()->json(['error' => 'Erro ao buscar atleta: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atleta $atleta)
    {
        try {
            // Log para debug
            \Log::info("Atualizando atleta ID: {$atleta->id}");
            \Log::info("Dados recebidos: " . json_encode($request->all()));
            \Log::info("Arquivos recebidos: " . json_encode($request->allFiles()));
            
            $validated = $request->validate([
                'nome' => 'sometimes|required|string|max:255',
                'idade' => 'sometimes|nullable|integer|min:0',
                'categoria' => 'sometimes|nullable|string|max:100',
                'foto' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'esporte_id' => 'sometimes|nullable|exists:esportes,id',
                'treinadores' => 'sometimes|nullable|array',
                'treinadores.*' => 'exists:treinadors,id'
            ]);
            
            \Log::info("Dados validados: " . json_encode($validated));
            
            if ($request->hasFile('foto')) {
                \Log::info("Arquivo de foto recebido: " . $request->file('foto')->getClientOriginalName());
                
                if ($atleta->foto) {
                    \Log::info("Excluindo foto anterior: {$atleta->foto}");
                    Storage::disk('public')->delete($atleta->foto);
                }
                
                $path = $request->file('foto')->store('atletas', 'public');
                \Log::info("Nova foto salva em: {$path}");
                $validated['foto'] = $path;
            }
            
            $atleta->update($validated);
            \Log::info("Atleta atualizado com sucesso: " . json_encode($atleta));
            
            if ($request->has('treinadores')) {
                \Log::info("Sincronizando treinadores: " . json_encode($request->treinadores));
                $atleta->treinadores()->sync($request->treinadores);
            }
            
            // Recarregar o modelo para garantir que temos os dados mais recentes
            $atleta->refresh();
            \Log::info("Dados finais do atleta: " . json_encode($atleta));
            
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
