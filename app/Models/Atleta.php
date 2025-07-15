<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Atleta extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'idade',
        'categoria',
        'foto',
        'esporte_id'
    ];

    public function esporte(): BelongsTo
    {
        return $this->belongsTo(Esporte::class);
    }

    public function treinadores(): BelongsToMany
    {
        return $this->belongsToMany(Treinador::class);
    }
}
