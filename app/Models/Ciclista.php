<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['equipo_id', 'nombre', 'apellidos', 'nacionalidad', 'fecha_nacimiento', 'activo'])]
class Ciclista extends Model
{
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function pruebas(): BelongsToMany
    {
        return $this->belongsToMany(Prueba::class, 'ciclista_prueba')
            ->withPivot(['equipo_id', 'dorsal', 'abandono', 'posicion_general'])
            ->withTimestamps()
            ->using(CiclistaPrueba::class);
    }

    public function tiempos(): HasMany
    {
        return $this->hasMany(Tiempo::class);
    }

    // En Ciclista.php
    public function getFechaNacimientoFormateadaAttribute(): string
    {
        return $this->fecha_nacimiento
            ? Carbon::parse($this->fecha_nacimiento)->format('d/m/Y')
            : '—';
    }

    public function getEdadAttribute(): string
    {
        return $this->fecha_nacimiento
            ? Carbon::parse($this->fecha_nacimiento)->age.' años'
            : '—';
    }
}
