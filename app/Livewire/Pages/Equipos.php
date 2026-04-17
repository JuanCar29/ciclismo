<?php

namespace App\Livewire\Pages;

use App\Models\Equipo;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Equipos')]
class Equipos extends Component
{
    use WithPagination;

    // ── Estado de la UI ──────────────────────────────────────────────
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $search = '';

    // ── Campos del formulario ────────────────────────────────────────
    #[Validate('required|string|max:255')]
    public string $nombre = '';

    #[Validate('nullable|string|max:10')]
    public string $abreviatura = '';

    #[Validate('nullable|string|size:3')]
    public string $pais = '';

    #[Validate('nullable|url|max:255')]
    public string $web = '';

    // ── Ciclo de vida ────────────────────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
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
        $equipo = Equipo::findOrFail($id);

        $this->editingId    = $equipo->id;
        $this->nombre       = $equipo->nombre;
        $this->abreviatura  = $equipo->abreviatura ?? '';
        $this->pais         = $equipo->pais ?? '';
        $this->web          = $equipo->web ?? '';

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nombre'      => $this->nombre,
            'abreviatura' => $this->abreviatura ?: null,
            'pais'        => $this->pais ?: null,
            'web'         => $this->web ?: null,
        ];

        if ($this->editingId) {
            Equipo::findOrFail($this->editingId)->update($data);
            Flux::toast(heading: 'Equipo actualizado', text: $this->nombre, variant: 'success');
        } else {
            Equipo::create($data);
            Flux::toast(heading: 'Equipo creado', text: $this->nombre, variant: 'success');
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
        $this->nombre      = '';
        $this->abreviatura = '';
        $this->pais        = '';
        $this->web         = '';
        $this->resetValidation();
    }

    // ── Render ────────────────────────────────────────────────────────
    public function render()
    {
        $equipos = Equipo::query()
            ->when($this->search, fn ($q) => $q->where('nombre', 'like', "%{$this->search}%")
                ->orWhere('abreviatura', 'like', "%{$this->search}%"))
            ->withCount('ciclistas')
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.pages.equipos', compact('equipos'));
    }
}
