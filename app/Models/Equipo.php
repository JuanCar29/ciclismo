<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'abreviatura', 'pais', 'web'])]
class Equipo extends Model
{
    public function ciclistas(): HasMany
    {
        return $this->hasMany(Ciclista::class);
    }

    public function pruebas(): HasMany
    {
        return $this->hasMany(CiclistaPrueba::class);
    }
}
