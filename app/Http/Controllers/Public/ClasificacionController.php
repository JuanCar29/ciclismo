<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CiclistaPrueba;
use App\Models\Etapa;
use App\Models\Prueba;
use App\Models\Tiempo;
use Illuminate\Support\Collection;

class ClasificacionController extends Controller
{
    /**
     * Clasificación general acumulada de la prueba.
     */
    public function general(Prueba $prueba)
    {
        $etapas = $prueba->etapas()->orderBy('numero')->get();
        $etapaIds = $etapas->pluck('id');

        $participantes = $this->obtenerParticipantes($prueba);

        $tiempos = Tiempo::query()
            ->whereIn('etapa_id', $etapaIds)
            ->with('ciclista')
            ->get();

        $clasificacion = $tiempos
            ->groupBy('ciclista_id')
            ->map(function ($tiemposCiclista) use ($participantes) {
                $ciclistaId = $tiemposCiclista->first()->ciclista_id;
                $participante = $participantes[$ciclistaId] ?? null;

                $totalNeto = $tiemposCiclista->sum(fn ($t) => $t->tiempoNeto());

                return [
                    'ciclista' => $tiemposCiclista->first()->ciclista,
                    'equipo' => $participante?->equipo,
                    'dorsal' => $participante?->dorsal,
                    'abandono' => $participante?->abandono,
                    'etapas' => $tiemposCiclista->count(),
                    'total_neto' => $totalNeto,
                    'formateado' => $this->formatearTiempo($totalNeto),
                ];
            });

        $activos = $clasificacion
            ->whereNull('abandono')
            ->sortBy('total_neto')
            ->values();

        $abandonados = $clasificacion
            ->whereNotNull('abandono')
            ->sortBy('abandono')
            ->values()
            ->map(fn ($item) => [
                ...$item,
                'diferencia' => 'AB',
            ]);

        $clasificacionFinal = $activos
            ->pipe(fn ($activosConDiferencias) => $this->agregarDiferencias($activosConDiferencias, 'total_neto'))
            ->concat($abandonados)
            ->map(fn ($item, $index) => [
                ...$item,
                'posicion' => $item['posicion'] ?? $index + 1,
            ]);

        return view('public.clasificaciones.general', [
            'prueba' => $prueba,
            'clasificacion' => $clasificacionFinal,
            'etapas' => $etapas,
        ]);
    }

    /**
     * Clasificación de una etapa concreta.
     */
    public function etapa(Prueba $prueba, Etapa $etapa)
    {
        $participantes = $this->obtenerParticipantes($prueba);

        $tiempos = Tiempo::query()
            ->where('etapa_id', $etapa->id)
            ->with('ciclista')
            ->orderBy('segundos')
            ->get();

        $tiempoLider = $tiempos->first()?->tiempoNeto() ?? 0;

        $clasificacion = $tiempos->map(function ($tiempo, $index) use ($participantes, $tiempoLider) {
            $participante = $participantes[$tiempo->ciclista_id] ?? null;
            $neto = $tiempo->tiempoNeto();
            $diff = $neto - $tiempoLider;

            return [
                'posicion' => $index + 1,
                'ciclista' => $tiempo->ciclista,
                'equipo' => $participante?->equipo,
                'dorsal' => $participante?->dorsal,
                'segundos' => $tiempo->segundos,
                'bonificacion' => $tiempo->bonificacion,
                'penalizacion' => $tiempo->penalizacion,
                'neto' => $neto,
                'formateado' => $this->formatearTiempo($neto),
                'diferencia' => $index === 0 ? null : '+'.$this->formatearTiempo($diff),
            ];
        });

        $etapas = $prueba->etapas()->orderBy('numero')->get();

        return view('public.clasificaciones.etapa', compact('prueba', 'etapa', 'clasificacion', 'etapas'));
    }

    /**
     * Clasificación por equipos — suma de los 3 mejores tiempos netos por equipo.
     * Solo aparecen equipos con al menos 3 ciclistas.
     */
    public function equipos(Prueba $prueba)
    {
        $etapas = $prueba->etapas()->orderBy('numero')->get();
        $etapaIds = $etapas->pluck('id');

        $participantes = $this->obtenerParticipantes($prueba);

        $tiempos = Tiempo::query()
            ->whereIn('etapa_id', $etapaIds)
            ->with('ciclista')
            ->get();

        // Contar ciclistas únicos por equipo
        $ciclistasPorEquipo = $participantes
            ->groupBy('equipo_id')
            ->filter(fn ($ciclistas, $equipoId) => $equipoId !== null)
            ->map(fn ($ciclistas) => $ciclistas->pluck('ciclista_id')->unique()->count());

        $tiemposPorCiclista = $tiempos
            ->groupBy('ciclista_id')
            ->map(fn ($t) => $t->sum(fn ($tiempo) => $tiempo->tiempoNeto()));

        $clasificacion = $tiemposPorCiclista
            ->groupBy(fn ($_, $ciclistaId) => $participantes[$ciclistaId]?->equipo_id)
            ->filter(fn ($_, $equipoId) => $equipoId !== null)
            ->map(function ($tiemposCiclistas, $equipoId) use ($participantes, $ciclistasPorEquipo) {
                // Excluir equipos con menos de 3 ciclistas
                if (($ciclistasPorEquipo[$equipoId] ?? 0) < 3) {
                    return null;
                }

                $equipo = $participantes->firstWhere('equipo_id', $equipoId)?->equipo;
                $tresMejores = $tiemposCiclistas->sort()->take(3);
                $totalEquipo = $tresMejores->sum();

                return [
                    'equipo' => $equipo,
                    'total_neto' => $totalEquipo,
                    'formateado' => $this->formatearTiempo($totalEquipo),
                    'corredores' => $tresMejores->count(),
                ];
            })
            ->filter() // Eliminar los null (equipos con menos de 3 ciclistas)
            ->sortBy('total_neto')
            ->values();

        $clasificacion = $this->agregarDiferencias($clasificacion, 'total_neto');

        return view('public.clasificaciones.equipos', compact('prueba', 'clasificacion', 'etapas'));
    }

    /**
     * Clasificación por puntos.
     */
    public function puntos(Prueba $prueba)
    {
        $etapas = $prueba->etapas()->orderBy('numero')->get();
        $etapaIds = $etapas->pluck('id');

        $participantes = $this->obtenerParticipantes($prueba);
        $participantesValidos = $participantes->whereNull('abandono')->keys();

        $puntos = Tiempo::query()
            ->whereIn('etapa_id', $etapaIds)
            ->whereIn('ciclista_id', $participantesValidos)
            ->with('ciclista')
            ->get();

        $clasificacion = $puntos
            ->groupBy('ciclista_id')
            ->map(function ($puntosCiclista, $ciclistaId) use ($participantes) {
                $participante = $participantes[$ciclistaId] ?? null;

                return [
                    'ciclista' => $puntosCiclista->first()->ciclista,
                    'equipo' => $participante?->equipo,
                    'dorsal' => $participante?->dorsal,
                    'etapas' => $puntosCiclista->count(),
                    'total_puntos' => $puntosCiclista->sum('puntos'),
                ];
            })
            ->filter(fn ($item) => $item['total_puntos'] > 0)
            ->sortByDesc('total_puntos')
            ->values()
            ->map(fn ($item, $index) => [
                ...$item,
                'posicion' => $index + 1,
            ]);

        return view('public.clasificaciones.puntos', compact('prueba', 'clasificacion', 'etapas'));
    }

    /**
     * Obtiene todos los participantes de una prueba indexados por ciclista_id.
     */
    private function obtenerParticipantes(Prueba $prueba): Collection
    {
        return CiclistaPrueba::where('prueba_id', $prueba->id)
            ->with('equipo')
            ->get()
            ->keyBy('ciclista_id');
    }

    /**
     * Agrega la diferencia respecto al líder a cada elemento de la colección.
     */
    private function agregarDiferencias(Collection $clasificacion, string $campo): Collection
    {
        $valorLider = $clasificacion->first()[$campo] ?? 0;

        return $clasificacion->map(function ($item, $index) use ($valorLider, $campo) {
            $diff = $item[$campo] - $valorLider;

            return [
                ...$item,
                'posicion' => $item['posicion'] ?? $index + 1,
                'diferencia' => $index === 0 ? null : '+'.$this->formatearTiempo($diff),
            ];
        });
    }

    /**
     * Formatea un tiempo en segundos a formato legible H:MM:SS.
     */
    private function formatearTiempo(int $segundos): string
    {
        $h = intdiv($segundos, 3600);
        $m = intdiv($segundos % 3600, 60);
        $s = $segundos % 60;

        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }
}
