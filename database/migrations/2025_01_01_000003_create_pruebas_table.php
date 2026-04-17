<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pruebas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('tipo', ['etapas', 'un_dia'])->default('etapas');
            $table->string('pais', 3)->nullable()->comment('Código ISO 3166-1 alpha-3');
            $table->integer('edicion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pruebas');
    }
};
