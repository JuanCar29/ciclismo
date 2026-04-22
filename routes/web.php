<?php

use App\Http\Controllers\Public\CiclistaPublicaController;
use App\Http\Controllers\Public\ClasificacionController;
use App\Http\Controllers\Public\EquipoPublicaController;
use App\Http\Controllers\Public\PruebaPublicaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/equipos', 'pages.equipos')->name('equipos');
    Route::livewire('/ciclistas', 'pages.ciclistas')->name('ciclistas');
    Route::livewire('/pruebas', 'pages.pruebas')->name('pruebas');
    Route::livewire('/pruebas/{prueba}/etapas', 'pages.etapas')->name('etapas');
    Route::livewire('/pruebas/{prueba}/participantes', 'pages.participantes')->name('participantes');
    Route::livewire('/pruebas/{prueba}/etapas/{etapa}/tiempos', 'pages.tiempos')->name('tiempos');
    Route::livewire('/users', 'pages::manageruser')->name('usuarios');
});

// ── Inicio ────────────────────────────────────────────────────────────
Route::controller(PruebaPublicaController::class)->group(function () {
    Route::get('/', 'inicio')->name('public.inicio');
});

// ── Publico ───────────────────────────────────────────────────────────
Route::prefix('resultados')->name('public.')->group(function () {

    // Pruebas
    Route::controller(PruebaPublicaController::class)->group(function () {
        Route::get('/pruebas', 'index')->name('pruebas.index');
        Route::get('/pruebas/{prueba}', 'show')->name('pruebas.show');
    });

    // Clasificaciones
    Route::controller(ClasificacionController::class)->group(function () {
        Route::get('/pruebas/{prueba}/clasificacion-general', 'general')->name('clasificacion.general');
        Route::get('/pruebas/{prueba}/clasificacion-puntos', 'puntos')->name('clasificacion.puntos');
        Route::get('/pruebas/{prueba}/clasificacion-equipos', 'equipos')->name('clasificacion.equipos');
        Route::get('/pruebas/{prueba}/etapas/{etapa}', 'etapa')->name('clasificacion.etapa');
    });

    // Ciclistas
    Route::controller(CiclistaPublicaController::class)->group(function () {
        Route::get('/ciclistas', 'index')->name('ciclistas.index');
        Route::get('/ciclistas/{ciclista}', 'show')->name('ciclistas.show');
    });

    // Equipos
    Route::controller(EquipoPublicaController::class)->group(function () {
        Route::get('/equipos', 'index')->name('equipos.index');
        Route::get('/equipos/{equipo}', 'show')->name('equipos.show');
    });
});

require __DIR__.'/settings.php';
