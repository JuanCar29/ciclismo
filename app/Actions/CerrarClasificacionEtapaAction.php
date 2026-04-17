<?php

namespace App\Actions;

use App\Models\Etapa;
use App\Models\Tiempo;
use Illuminate\Support\Collection;

class CerrarClasificacionEtapaAction
{
    public function execute(Etapa $etapa): int
    {
        // Obtener tiempos ordenados por tiempo neto ascendente
        $tiempos = $etapa->tiempos()
            ->orderByRaw('(segundos - bonificacion + penalizacion) ASC')
            ->get();

        if ($tiempos->isEmpty()) {
            return 0;
        }

        // Preparar ids y posiciones
        $ids = $tiempos->pluck('id');
        $positions = range(1, $tiempos->count());

        // Actualizar posiciones con CASE WHEN en una sola consulta
        $caseSql = 'CASE id ';
        $bindings = [];
        foreach ($tiempos as $posicion => $tiempo) {
            $caseSql .= 'WHEN ? THEN ? ';
            $bindings[] = $tiempo->id;
            $bindings[] = $posicion + 1;
        }
        $caseSql .= 'END';

        $bindings[] = $etapa->id;

        \Illuminate\Support\Facades\DB::statement(
            "UPDATE tiempos SET posicion = {$caseSql} WHERE etapa_id = ? AND id IN (" . $ids->implode(',') . ')',
            $bindings
        );

        return $tiempos->count();
    }
}
