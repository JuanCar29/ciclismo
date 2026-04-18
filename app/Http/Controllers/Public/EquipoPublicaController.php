<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\CiclistaPrueba;
use App\Models\Tiempo;
use Illuminate\Http\Request;

class EquipoPublicaController extends Controller
{
    /**
     * Listado público de equipos.
     */
    public function index(Request $request)
    {
        $equipos = Equipo::query()
            ->withCount('ciclistas')
            ->orderBy('nombre')
            ->paginate(12);

        return view('public.equipos.index', compact('equipos'));
    }

    /**
     * Perfil público de un equipo con sus ciclistas y participaciones en pruebas.
     */
    public function show(Equipo $equipo)
    {
        // Ciclistas actualmente en el equipo
        $ciclistas = $equipo->ciclistas()
            ->where('activo', true)
            ->orderBy('apellidos')
            ->get();

        // Participaciones del equipo en pruebas (a través de CiclistaPrueba)
        $participaciones = CiclistaPrueba::where('equipo_id', $equipo->id)
            ->with(['prueba', 'ciclista'])
            ->get()
            ->groupBy('prueba_id')
            ->map(function ($items) {
                $prueba = $items->first()->prueba;
                return [
                    'prueba' => $prueba,
                    'ciclistas_count' => $items->count(),
                    'mejor_posicion' => $items->whereNotNull('posicion_general')->min('posicion_general'),
                    'abandonos' => $items->whereNotNull('abandono')->count(),
                ];
            })
            ->sortByDesc(fn($p) => $p['prueba']->fecha_inicio);

        // -- Mejor ciclista del equipo en cada etapa --
        $mejoresPorEtapa = Tiempo::query()
            ->join('etapas', 'tiempos.etapa_id', '=', 'etapas.id')
            ->join('ciclista_prueba', function ($join) use ($equipo) {
                $join->on('tiempos.ciclista_id', '=', 'ciclista_prueba.ciclista_id')
                    ->on('etapas.prueba_id', '=', 'ciclista_prueba.prueba_id')
                    ->where('ciclista_prueba.equipo_id', '=', $equipo->id);
            })
            ->select('tiempos.*')
            ->with(['ciclista', 'etapa.prueba'])
            ->get()
            ->groupBy('etapa_id')
            ->map(function ($tiemposEtapa) {
                return $tiemposEtapa->sortBy('posicion')->first();
            })
            ->sortByDesc(fn($t) => $t->etapa->fecha) // Por fecha de etapa descendente
            ->take(20);

        return view('public.equipos.show', compact('equipo', 'ciclistas', 'participaciones', 'mejoresPorEtapa'));
    }
}
