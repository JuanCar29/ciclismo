<?php

use App\Http\Controllers\Public\CiclistaPublicaController;
use App\Http\Controllers\Public\ClasificacionController;
use App\Http\Controllers\Public\PruebaPublicaController;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');

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
Route::get('/', [PruebaPublicaController::class, 'inicio'])->name('public.inicio');

// ── Pruebas ───────────────────────────────────────────────────────────
Route::prefix('resultados')->name('public.')->group(function () {

    Route::get('/pruebas', [PruebaPublicaController::class, 'index'])
        ->name('pruebas.index');

    Route::get('/pruebas/{prueba}', [PruebaPublicaController::class, 'show'])
        ->name('pruebas.show');

    // Clasificaciones
    Route::get('/pruebas/{prueba}/clasificacion-general', [ClasificacionController::class, 'general'])
        ->name('clasificacion.general');

    Route::get('/pruebas/{prueba}/clasificacion-puntos', [ClasificacionController::class, 'puntos'])
        ->name('clasificacion.puntos');

    Route::get('/pruebas/{prueba}/clasificacion-equipos', [ClasificacionController::class, 'equipos'])
        ->name('clasificacion.equipos');

    Route::get('/pruebas/{prueba}/etapas/{etapa}', [ClasificacionController::class, 'etapa'])
        ->name('clasificacion.etapa');

    // Ciclistas
    Route::get('/ciclistas', [CiclistaPublicaController::class, 'index'])
        ->name('ciclistas.index');

    Route::get('/ciclistas/{ciclista}', [CiclistaPublicaController::class, 'show'])
        ->name('ciclistas.show');

    // Equipos
    Route::get('/equipos', [\App\Http\Controllers\Public\EquipoPublicaController::class, 'index'])
        ->name('equipos.index');

    Route::get('/equipos/{equipo}', [\App\Http\Controllers\Public\EquipoPublicaController::class, 'show'])
        ->name('equipos.show');
});

require __DIR__.'/settings.php';
