<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Prueba;

class PruebaPublicaController extends Controller
{
    /**
     * Página de inicio — pruebas en curso y próximas destacadas.
     */
    public function inicio()
    {
        $enCurso = Prueba::query()
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->withCount('etapas')
            ->orderBy('fecha_inicio')
            ->get();

        $proximas = Prueba::query()
            ->where('fecha_inicio', '>', now())
            ->withCount('etapas')
            ->orderBy('fecha_inicio')
            ->take(4)
            ->get();

        $pasadas = Prueba::query()
            ->where('fecha_fin', '<', now())
            ->withCount('etapas')
            ->orderBy('fecha_fin', 'desc')
            ->take(6)
            ->get();

        return view('public.inicio', compact('enCurso', 'proximas', 'pasadas'));
    }

    /**
     * Listado completo de pruebas.
     */
    public function index()
    {
        $pruebas = Prueba::query()
            ->withCount('etapas')
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return view('public.pruebas.index', compact('pruebas'));
    }

    /**
     * Detalle de una prueba con sus etapas.
     */
    public function show(Prueba $prueba)
    {
        $etapas = $prueba->etapas()
            ->withCount('tiempos')
            ->orderBy('numero')
            ->get();

        $totalParticipantes = $prueba->ciclistas()->count();

        $abandonos = $prueba->ciclistas()
            ->wherePivotNotNull('abandono')
            ->count();

        return view('public.pruebas.show', compact('prueba', 'etapas', 'totalParticipantes', 'abandonos'));
    }
}
