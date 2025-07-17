<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtletaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'idade' => $this->idade,
            'categoria' => $this->categoria,
            'foto' => $this->foto,
            'image_url' => $this->image_url,
            'esporte_id' => $this->esporte_id,
            'esporte' => $this->whenLoaded('esporte', function() {
                return [
                    'id' => $this->esporte->id,
                    'nome' => $this->esporte->nome,
                    'federacao' => $this->esporte->federacao
                ];
            }),
            'treinadores' => $this->whenLoaded('treinadores', function() {
                return $this->treinadores->map(function($treinador) {
                    return [
                        'id' => $treinador->id,
                        'nome' => $treinador->nome,
                        'cref' => $treinador->cref,
                        'especialidade' => $treinador->especialidade,
                        'image_url' => $treinador->image_url ?? null
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
