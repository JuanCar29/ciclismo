<?php

namespace App\Models;

use App\Enums\TipoEtapa;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['prueba_id', 'numero', 'nombre', 'salida', 'llegada', 'distancia_km', 'tipo', 'fecha'])]
class Etapa extends Model
{
    protected function casts(): array
    {
        return [
            'fecha'         => 'date',
            'distancia_km'  => 'decimal:2',
            'tipo'          => TipoEtapa::class,
        ];
    }

    public function prueba(): BelongsTo
    {
        return $this->belongsTo(Prueba::class);
    }

    public function tiempos(): HasMany
    {
        return $this->hasMany(Tiempo::class);
    }

    /**
     * Calcula la velocidad media del ganador de la etapa.
     */
    protected function velocidadMediaGanador(): Attribute
    {
        return Attribute::make(
            get: function () {
                $mejorTiempo = $this->tiempos()->orderBy('segundos')->first();

                if (!$mejorTiempo || $this->distancia_km <= 0 || $mejorTiempo->segundos <= 0) {
                    return null;
                }

                $velocidad = $this->distancia_km / ($mejorTiempo->segundos / 3600);

                return number_format($velocidad, 2, ',', '.') . ' km/h';
            },
        );
    }
}
