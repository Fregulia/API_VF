<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treinadors', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cref')->unique();
            $table->string('especialidade')->nullable();
            $table->foreignId('esporte_id')->nullable()->constrained('esportes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treinadors');
    }
};