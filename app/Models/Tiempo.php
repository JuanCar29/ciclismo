<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ciclista_id', 'etapa_id', 'segundos', 'bonificacion', 'penalizacion', 'puntos'])]
class Tiempo extends Model
{
    protected function casts(): array
    {
        return [
            'segundos'     => 'integer',
            'bonificacion' => 'integer',
            'penalizacion' => 'integer',
            'puntos'       => 'integer',
        ];
    }

    public function ciclista(): BelongsTo
    {
        return $this->belongsTo(Ciclista::class);
    }

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class);
    }

    /**
     * Tiempo neto en segundos (bruto - bonificación + penalización)
     */
    public function tiempoNeto(): int
    {
        return $this->segundos - $this->bonificacion + $this->penalizacion;
    }

    /**
     * Formatea segundos a H:MM:SS
     */
    public function tiempoFormateado(): string
    {
        $neto = $this->tiempoNeto();
        $h    = intdiv($neto, 3600);
        $m    = intdiv($neto % 3600, 60);
        $s    = $neto % 60;

        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }
}
