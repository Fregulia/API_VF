<?php

namespace App\Models;

use App\Traits\HasDefaultImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Treinador extends Model
{
    use HasFactory, HasDefaultImage;
    
    protected $fillable = [
        'nome',
        'cref',
        'especialidade',
        'foto',
        'esporte_id'
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

    public function atletas(): BelongsToMany
    {
        return $this->belongsToMany(Atleta::class);
    }
}
