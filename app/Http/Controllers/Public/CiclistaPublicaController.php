<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Ciclista;
use App\Models\Equipo;
use App\Models\Tiempo;
use Illuminate\Http\Request;

class CiclistaPublicaController extends Controller
{
    /**
     * Listado público de ciclistas activos.
     */
    public function index(Request $request)
    {
        $ciclistas = Ciclista::query()
            ->with('equipo')
            ->where('activo', true)
            ->when($request->filled('equipo_id'), function ($query) use ($request) {
                $query->where('equipo_id', $request->equipo_id);
            })
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->paginate(20)
            ->appends($request->only('equipo_id'));

        $equipos = Equipo::query()
            ->orderBy('nombre')
            ->get();

        return view('public.ciclistas.index', compact('ciclistas', 'equipos'));
    }

    /**
     * Perfil público de un ciclista con su historial de pruebas.
     */
    public function show(Ciclista $ciclista)
    {
        $ciclista->load('equipo');

        // ── Pruebas en las que ha participado ──────────────────────────────
        // Cargar pruebas con sus etapas y el equipo del pivot
        $participaciones = $ciclista->pruebas()
            ->with(['etapas'])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // Cargar todos los equipos de una vez para evitar N+1 queries
        $equiposIds = $participaciones->pluck('pivot.equipo_id')->filter()->unique();
        $equiposMap = Equipo::whereIn('id', $equiposIds)->get()->keyBy('id');

        $participaciones = $participaciones->map(function ($prueba) use ($ciclista, $equiposMap) {
            $etapasIds = $prueba->etapas->pluck('id');

            $tiempos = Tiempo::where('ciclista_id', $ciclista->id)
                ->whereIn('etapa_id', $etapasIds)
                ->get();

            $totalNeto = $tiempos->sum(fn ($t) => $t->tiempoNeto());
            $etapasCorr = $tiempos->count();

            return [
                'prueba' => $prueba,
                'dorsal' => $prueba->pivot->dorsal,
                'equipo_abreviatura' => $equiposMap->get($prueba->pivot->equipo_id)?->abreviatura ?? '—',
                'abandono' => $prueba->pivot->abandono,
                'posicion' => $prueba->pivot->posicion_general,
                'etapas' => $etapasCorr,
                'total_neto' => $totalNeto,
                'formateado' => $totalNeto > 0 ? $this->formatearTiempo($totalNeto) : '—',
            ];
        });

        // ── Etapas concluidas ─────────────────────────────────────────────
        $etapasConcluidas = $ciclista->tiempos()
            ->with(['etapa.prueba'])
            ->join('etapas', 'tiempos.etapa_id', '=', 'etapas.id')
            ->orderBy('etapas.fecha', 'desc')
            ->get('tiempos.*')
            ->map(function ($tiempo) {
                return [
                    'etapa' => $tiempo->etapa,
                    'prueba' => $tiempo->etapa->prueba,
                    'fecha' => $tiempo->etapa->fecha,
                    'posicion' => $tiempo->posicion,
                    'tiempo_neto' => $tiempo->tiempoNeto(),
                    'formateado' => $this->formatearTiempo($tiempo->tiempoNeto()),
                ];
            });

        return view('public.ciclistas.show', compact('ciclista', 'participaciones', 'etapasConcluidas'));
    }

    // ── Helper ────────────────────────────────────────────────────────
    private function formatearTiempo(int $segundos): string
    {
        $h = intdiv($segundos, 3600);
        $m = intdiv($segundos % 3600, 60);
        $s = $segundos % 60;

        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }
}
