<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclista_prueba', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclista_id')->constrained('ciclistas')->cascadeOnDelete();
            $table->foreignId('prueba_id')->constrained('pruebas')->cascadeOnDelete();
            $table->foreignId('equipo_id')->nullable()->constrained('equipos')->nullOnDelete();
            $table->unsignedSmallInteger('dorsal')->nullable();
            $table->unsignedSmallInteger('abandono')->nullable()->comment('null = en carrera, número de etapa en la que abandonó');
            $table->unsignedSmallInteger('posicion_general')->nullable();
            $table->timestamps();

            $table->unique(['ciclista_id', 'prueba_id']);
            $table->unique(['prueba_id', 'dorsal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclista_prueba');
    }
};
