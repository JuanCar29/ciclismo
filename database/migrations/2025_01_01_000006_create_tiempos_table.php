<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiempos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclista_id')->constrained('ciclistas')->cascadeOnDelete();
            $table->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $table->unsignedInteger('segundos')->comment('Tiempo bruto en segundos');
            $table->smallInteger('bonificacion')->default(0)->comment('Segundos a restar (positivo = bonificación)');
            $table->smallInteger('penalizacion')->default(0)->comment('Segundos a sumar como sanción');
            $table->unsignedSmallInteger('puntos')->default(0)->comment('Puntos obtenidos en la etapa');
            $table->unsignedSmallInteger('posicion')->nullable()->comment('Posición final en la etapa');
            $table->timestamps();

            $table->unique(['ciclista_id', 'etapa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiempos');
    }
};
