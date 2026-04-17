<?php

namespace App\Livewire\Pages;

use App\Models\CiclistaPrueba;
use App\Models\Etapa;
use App\Models\Prueba;
use App\Models\Tiempo;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tiempos')]
class Tiempos extends Component
{
    // ── Props de contexto ────────────────────────────────────────────
    public int $prueba_id;
    public string $pruebaNombre = '';
    public int $etapa_id;
    public int $etapaNumero;
    public string $etapaNombre = '';

    // ── Formulario rápido ────────────────────────────────────────────
    public ?int $dorsal       = null;
    public int  $horas        = 0;
    public int  $minutos      = 0;
    public int  $segundos     = 0;
    public int  $bonificacion = 0;
    public int  $penalizacion = 0;
    public int  $puntos       = 0;

    // ── Edición modal ────────────────────────────────────────────────
    public bool $showModal       = false;
    public ?int $editingId       = null;
    public int  $edit_horas      = 0;
    public int  $edit_minutos    = 0;
    public int  $edit_segundos   = 0;
    public int  $edit_bonificacion = 0;
    public int  $edit_penalizacion = 0;
    public int  $edit_puntos       = 0;

    // ── Ciclo de vida ────────────────────────────────────────────────
    public function mount(Prueba $prueba, Etapa $etapa): void
    {
        $this->prueba_id    = $prueba->id;
        $this->pruebaNombre = $prueba->nombre;
        $this->etapa_id     = $etapa->id;
        $this->etapaNumero  = $etapa->numero;
        $this->etapaNombre  = $etapa->nombre ?? '';
    }

    // ── Computed ─────────────────────────────────────────────────────
    #[Computed]
    public function tiempos()
    {
        return Tiempo::with(['ciclista'])
            ->where('etapa_id', $this->etapa_id)
            ->orderBy('segundos')
            ->get();
    }

    // ── Acciones formulario rápido ───────────────────────────────────
    public function registrar(): void
    {
        $this->validate([
            'dorsal'       => 'required|integer|min:1|max:999',
            'horas'        => 'required|integer|min:0|max:23',
            'minutos'      => 'required|integer|min:0|max:59',
            'segundos'     => 'required|integer|min:0|max:59',
            'bonificacion' => 'nullable|integer|min:0|max:999',
            'penalizacion' => 'nullable|integer|min:0|max:999',
            'puntos'       => 'nullable|integer|min:0|max:999',
        ]);

        // Resolver ciclista por dorsal dentro de la prueba
        $participante = CiclistaPrueba::with('ciclista')
            ->where('prueba_id', $this->prueba_id)
            ->where('dorsal', $this->dorsal)
            ->first();

        if (! $participante) {
            $this->addError('dorsal', "No existe el dorsal {$this->dorsal} en esta prueba.");
            return;
        }

        $totalSegundos = ($this->horas * 3600) + ($this->minutos * 60) + $this->segundos;

        // Crear o actualizar si ya existe (permite corregir)
        Tiempo::updateOrCreate(
            [
                'ciclista_id' => $participante->ciclista_id,
                'etapa_id'    => $this->etapa_id,
            ],
            [
                'segundos'     => $totalSegundos,
                'bonificacion' => $this->bonificacion,
                'penalizacion' => $this->penalizacion,
                'puntos'       => $this->puntos,
            ]
        );

        Flux::toast(
            heading: "Dorsal {$this->dorsal}",
            text: "{$participante->ciclista->apellidos} — " . $this->formatearTiempo($totalSegundos),
            variant: 'success'
        );

        // Limpiar solo el dorsal, mantener h/m/s como base
        $this->dorsal = null;
        $this->resetValidation();
        unset($this->tiempos);
    }

    // ── Acciones edición modal ───────────────────────────────────────
    public function edit(int $id): void
    {
        $tiempo = Tiempo::findOrFail($id);

        $this->editingId         = $tiempo->id;
        $this->edit_horas        = intdiv($tiempo->segundos, 3600);
        $this->edit_minutos      = intdiv($tiempo->segundos % 3600, 60);
        $this->edit_segundos     = $tiempo->segundos % 60;
        $this->edit_bonificacion = $tiempo->bonificacion;
        $this->edit_penalizacion = $tiempo->penalizacion;
        $this->edit_puntos       = $tiempo->puntos;

        $this->showModal = true;
    }

    public function update(): void
    {
        $this->validate([
            'edit_horas'        => 'required|integer|min:0|max:23',
            'edit_minutos'      => 'required|integer|min:0|max:59',
            'edit_segundos'     => 'required|integer|min:0|max:59',
            'edit_bonificacion' => 'nullable|integer|min:0|max:999',
            'edit_penalizacion' => 'nullable|integer|min:0|max:999',
            'edit_puntos'       => 'nullable|integer|min:0|max:999',
        ]);

        $totalSegundos = ($this->edit_horas * 3600) + ($this->edit_minutos * 60) + $this->edit_segundos;

        Tiempo::findOrFail($this->editingId)->update([
            'segundos'     => $totalSegundos,
            'bonificacion' => $this->edit_bonificacion,
            'penalizacion' => $this->edit_penalizacion,
            'puntos'       => $this->edit_puntos,
        ]);

        Flux::toast(
            heading: 'Tiempo actualizado',
            text: $this->formatearTiempo($totalSegundos),
            variant: 'success'
        );

        $this->showModal = false;
        $this->editingId = null;
        unset($this->tiempos);
    }

    public function delete(int $id): void
    {
        Tiempo::findOrFail($id)->delete();
        Flux::toast(heading: 'Tiempo eliminado', variant: 'warning');
        unset($this->tiempos);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    // ── Helpers ───────────────────────────────────────────────────────
    public function formatearTiempo(int $segundos): string
    {
        $h = intdiv($segundos, 3600);
        $m = intdiv($segundos % 3600, 60);
        $s = $segundos % 60;

        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }

    // ── Render ────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.tiempos');
    }
}
