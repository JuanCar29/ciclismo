<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->nullable()->constrained('equipos')->nullOnDelete();
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('nacionalidad', 3)->nullable()->comment('Código ISO 3166-1 alpha-3');
            $table->date('fecha_nacimiento')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclistas');
    }
};
