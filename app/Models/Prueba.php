<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['nombre', 'slug', 'fecha_inicio', 'fecha_fin', 'tipo', 'pais', 'edicion'])]
class Prueba extends Model
{
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::slug($value),
        );
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(Etapa::class)->orderBy('numero');
    }

    public function ciclistas(): BelongsToMany
    {
        return $this->belongsToMany(Ciclista::class, 'ciclista_prueba')
            ->withPivot(['equipo_id', 'dorsal', 'abandono', 'posicion_general'])
            ->withTimestamps()
            ->using(CiclistaPrueba::class);
    }
}
