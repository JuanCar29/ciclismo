<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prueba_id')->constrained('pruebas')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero');
            $table->string('nombre')->nullable();
            $table->string('salida')->nullable();
            $table->string('llegada')->nullable();
            $table->decimal('distancia_km', 6, 2)->nullable();
            $table->enum('tipo', ['llano', 'media_montana', 'alta_montana', 'contrarreloj', 'contrarreloj_por_equipos'])->default('llano');
            $table->date('fecha');
            $table->timestamps();

            $table->unique(['prueba_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapas');
    }
};
