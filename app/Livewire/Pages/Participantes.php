<?php

namespace App\Livewire\Pages;

use App\Models\Ciclista;
use App\Models\CiclistaPrueba;
use App\Models\Equipo;
use App\Models\Prueba;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Participantes')]
class Participantes extends Component
{
    // ── Props de contexto ────────────────────────────────────────────
    public int $prueba_id;
    public string $pruebaNombre = '';

    // ── Estado de la UI ──────────────────────────────────────────────
    public bool $showModal = false;
    public ?int $editingId = null; // id de ciclista_prueba
    public string $search = '';

    // ── Campos del formulario ────────────────────────────────────────
    #[Validate('required|exists:ciclistas,id')]
    public ?int $ciclista_id = null;

    #[Validate('required|exists:equipos,id')]
    public ?int $equipo_id = null;

    #[Validate('required|integer|min:1|max:999')]
    public ?int $dorsal = null;

    #[Validate('nullable|integer|min:1|max:999')]
    public ?int $abandono = null;

    // ── Ciclo de vida ────────────────────────────────────────────────
    public function mount(Prueba $prueba): void
    {
        $this->prueba_id    = $prueba->id;
        $this->pruebaNombre = $prueba->nombre;
    }

    public function updatingSearch(): void
    {
        // No hay paginación pero limpiamos por si acaso
    }

    // Al seleccionar ciclista, prerellenar equipo actual
    public function updatedCiclistaId(?int $value): void
    {
        if ($value) {
            $ciclista = Ciclista::find($value);
            if ($ciclista?->equipo_id) {
                $this->equipo_id = $ciclista->equipo_id;
            }
        }
    }

    // ── Computed ─────────────────────────────────────────────────────
    #[Computed(cache: true)]
    public function ciclistas(): array
    {
        return Ciclista::where('activo', true)
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellidos'])
            ->map(fn ($c) => [
                'value' => $c->id,
                'label' => "{$c->apellidos}, {$c->nombre}",
            ])
            ->toArray();
    }

    #[Computed(cache: true)]
    public function equipos(): array
    {
        return Equipo::orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn ($e) => ['value' => $e->id, 'label' => $e->nombre])
            ->toArray();
    }

    // ── Acciones ─────────────────────────────────────────────────────
    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;

        // Sugerir siguiente dorsal disponible
        $ultimo = CiclistaPrueba::where('prueba_id', $this->prueba_id)->max('dorsal');
        $this->dorsal = $ultimo ? $ultimo + 1 : 1;

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $participante = CiclistaPrueba::findOrFail($id);

        $this->editingId   = $participante->id;
        $this->ciclista_id = $participante->ciclista_id;
        $this->equipo_id   = $participante->equipo_id;
        $this->dorsal      = $participante->dorsal;
        $this->abandono    = $participante->abandono;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'equipo_id' => $this->equipo_id,
            'dorsal'    => $this->dorsal,
            'abandono'  => $this->abandono,
        ];

        if ($this->editingId) {
            CiclistaPrueba::findOrFail($this->editingId)->update($data);
            Flux::toast(heading: 'Participante actualizado', text: "Dorsal {$this->dorsal}", variant: 'success');
        } else {
            // Verificar que el ciclista no está ya inscrito
            $existe = CiclistaPrueba::where('prueba_id', $this->prueba_id)
                ->where('ciclista_id', $this->ciclista_id)
                ->exists();

            if ($existe) {
                $this->addError('ciclista_id', 'Este ciclista ya está inscrito en la prueba.');
                return;
            }

            CiclistaPrueba::create([
                'prueba_id'  => $this->prueba_id,
                'ciclista_id' => $this->ciclista_id,
                ...$data,
            ]);

            Flux::toast(heading: 'Participante inscrito', text: "Dorsal {$this->dorsal}", variant: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // ── Helpers ───────────────────────────────────────────────────────
    private function resetForm(): void
    {
        $this->ciclista_id = null;
        $this->equipo_id   = null;
        $this->dorsal      = null;
        $this->abandono    = null;
        $this->resetValidation();
    }

    // ── Render ────────────────────────────────────────────────────────
    public function render()
    {
        $participantes = CiclistaPrueba::with(['ciclista', 'equipo'])
            ->where('prueba_id', $this->prueba_id)
            ->when($this->search, fn ($q) => $q->whereHas('ciclista', fn ($q) => $q
                ->where('nombre', 'like', "%{$this->search}%")
                ->orWhere('apellidos', 'like', "%{$this->search}%")
            ))
            ->orderBy('dorsal')
            ->get();

        return view('livewire.pages.participantes', compact('participantes'));
    }
}
