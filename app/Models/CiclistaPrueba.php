<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['ciclista_id', 'prueba_id', 'equipo_id', 'dorsal', 'abandono', 'posicion_general'])]
class CiclistaPrueba extends Pivot
{
    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'dorsal'           => 'integer',
            'abandono'         => 'integer',
            'posicion_general' => 'integer',
        ];
    }

    public function ciclista(): BelongsTo
    {
        return $this->belongsTo(Ciclista::class);
    }

    public function prueba(): BelongsTo
    {
        return $this->belongsTo(Prueba::class);
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }
}
