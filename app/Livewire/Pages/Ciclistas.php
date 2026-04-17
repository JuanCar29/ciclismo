<?php

namespace App\Livewire\Pages;

use App\Models\Ciclista;
use App\Models\Equipo;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Ciclistas')]
class Ciclistas extends Component
{
    use WithPagination;

    // ── Estado de la UI ──────────────────────────────────────────────
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $search = '';

    // ── Campos del formulario ────────────────────────────────────────
    #[Validate('required|string|max:255')]
    public string $nombre = '';

    #[Validate('required|string|max:255')]
    public string $apellidos = '';

    #[Validate('nullable|exists:equipos,id')]
    public ?int $equipo_id = null;

    #[Validate('nullable|string|size:3')]
    public string $nacionalidad = '';

    #[Validate('nullable|date|before:today')]
    public string $fecha_nacimiento = '';

    // ── Ciclo de vida ────────────────────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ── Computed ─────────────────────────────────────────────────────
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
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $ciclista = Ciclista::findOrFail($id);

        $this->editingId        = $ciclista->id;
        $this->nombre           = $ciclista->nombre;
        $this->apellidos        = $ciclista->apellidos;
        $this->equipo_id        = $ciclista->equipo_id;
        $this->nacionalidad     = $ciclista->nacionalidad ?? '';
        $this->fecha_nacimiento = $ciclista->fecha_nacimiento?->format('Y-m-d') ?? '';

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nombre'           => $this->nombre,
            'apellidos'        => $this->apellidos,
            'equipo_id'        => $this->equipo_id,
            'nacionalidad'     => $this->nacionalidad ?: null,
            'fecha_nacimiento' => $this->fecha_nacimiento ?: null,
        ];

        if ($this->editingId) {
            Ciclista::findOrFail($this->editingId)->update($data);
            Flux::toast(heading: 'Ciclista actualizado', text: "{$this->nombre} {$this->apellidos}", variant: 'success');
        } else {
            Ciclista::create($data);
            Flux::toast(heading: 'Ciclista creado', text: "{$this->nombre} {$this->apellidos}", variant: 'success');
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
        $this->nombre           = '';
        $this->apellidos        = '';
        $this->equipo_id        = null;
        $this->nacionalidad     = '';
        $this->fecha_nacimiento = '';
        $this->resetValidation();
    }

    // ── Render ────────────────────────────────────────────────────────
    public function render()
    {
        $ciclistas = Ciclista::query()
            ->with('equipo')
            ->when($this->search, fn ($q) => $q
                ->where('nombre', 'like', "%{$this->search}%")
                ->orWhere('apellidos', 'like', "%{$this->search}%")
                ->orWhereHas('equipo', fn ($q) => $q->where('nombre', 'like', "%{$this->search}%"))
            )
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.pages.ciclistas', compact('ciclistas'));
    }
}
