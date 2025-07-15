<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atleta_treinador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atleta_id')->constrained('atletas')->onDelete('cascade');
            $table->foreignId('treinador_id')->constrained('treinadors')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['atleta_id', 'treinador_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atleta_treinador');
    }
};