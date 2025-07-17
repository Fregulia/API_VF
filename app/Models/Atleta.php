<?php

namespace App\Models;

use App\Traits\HasDefaultImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Atleta extends Model
{
    use HasFactory, HasDefaultImage;
    
    // Definir explicitamente o nome da tabela
    protected $table = 'atletas';
    
    protected $fillable = [
        'nome',
        'idade',
        'categoria',
        'foto',
        'esporte_id'
    ];
    
    // Garantir que os atributos sejam do tipo correto
    protected $casts = [
        'idade' => 'integer',
        'esporte_id' => 'integer',
    ];
    
    protected $appends = ['image_url'];    
    
    public function getImageUrlAttribute()
    {
        $url = $this->getImageUrl();
        \Log::info("URL da imagem gerada para {$this->nome}: {$url}");
        return $url;
    }

    public function esporte(): BelongsTo
    {
        return $this->belongsTo(Esporte::class);
    }

    public function treinadores(): BelongsToMany
    {
        return $this->belongsToMany(Treinador::class);
    }
}
