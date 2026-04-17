<?php

namespace App\Actions;

use App\Models\CiclistaPrueba;
use App\Models\Prueba;
use App\Models\Tiempo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CerrarClasificacionGeneralAction
{
    public function execute(Prueba $prueba): int
    {
        $etapas = $prueba->etapas()->pluck('id');

        // Obtener IDs de ciclistas que abandonaron
        $abandonos = CiclistaPrueba::where('prueba_id', $prueba->id)
            ->whereNotNull('abandono')
            ->pluck('ciclista_id');

        // Sumar tiempos netos por ciclista — solo finalizados (sin abandono)
        $tiempos = Tiempo::whereIn('etapa_id', $etapas)
            ->whereNotIn('ciclista_id', $abandonos)
            ->selectRaw('ciclista_id, SUM(segundos - bonificacion + penalizacion) as total')
            ->groupBy('ciclista_id')
            ->orderBy('total')
            ->get()
            ->pluck('total', 'ciclista_id');

        // Resetear posicion_general de todos antes de recalcular
        CiclistaPrueba::where('prueba_id', $prueba->id)
            ->update(['posicion_general' => null]);

        // Asignar posiciones usando un solo UPDATE con CASE
        $this->actualizarPosiciones($prueba, $tiempos);

        return $tiempos->count();
    }

    private function actualizarPosiciones(Prueba $prueba, Collection $tiempos): void
    {
        $posicion = 1;
        $cases = [];
        $ids = [];

        foreach ($tiempos as $ciclistaId => $_) {
            $cases[] = "WHEN {$ciclistaId} THEN {$posicion}";
            $ids[] = $ciclistaId;
            $posicion++;
        }

        if (empty($ids)) {
            return;
        }

        $idsStr = implode(',', $ids);
        $casesStr = implode(' ', $cases);

        DB::update(
            "UPDATE ciclista_prueba
             SET posicion_general = CASE ciclista_id {$casesStr} END
             WHERE prueba_id = ?
             AND ciclista_id IN ({$idsStr})",
            [$prueba->id]
        );
    }
}
