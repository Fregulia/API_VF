<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Treinador extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'cref',
        'especialidade',
        'esporte_id'
    ];

    public function esporte(): BelongsTo
    {
        return $this->belongsTo(Esporte::class);
    }

    public function atletas(): BelongsToMany
    {
        return $this->belongsToMany(Atleta::class);
    }
}
