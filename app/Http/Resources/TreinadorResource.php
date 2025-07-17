<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreinadorResource extends JsonResource
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
            'cref' => $this->cref,
            'especialidade' => $this->especialidade,
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
            'atletas' => $this->whenLoaded('atletas', function() {
                return $this->atletas->map(function($atleta) {
                    return [
                        'id' => $atleta->id,
                        'nome' => $atleta->nome,
                        'idade' => $atleta->idade,
                        'categoria' => $atleta->categoria,
                        'image_url' => $atleta->image_url ?? null
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
