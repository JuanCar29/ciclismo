<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
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
}
