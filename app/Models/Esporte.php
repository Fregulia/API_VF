<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Esporte extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'federacao',
        'descricao'
    ];

    public function atletas(): HasMany
    {
        return $this->hasMany(Atleta::class);
    }

    public function treinadores(): HasMany
    {
        return $this->hasMany(Treinador::class);
    }
}
