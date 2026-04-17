<?php

namespace App\Livewire\Pages;

use App\Actions\CerrarClasificacionGeneralAction;
use App\Models\Prueba;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pruebas')]
class Pruebas extends Component
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
    public string $slug = '';

    #[Validate('required|in:etapas,un_dia')]
    public string $tipo = 'etapas';

    #[Validate('required|date')]
    public string $fecha_inicio = '';

    #[Validate('required|date|after_or_equal:fecha_inicio')]
    public string $fecha_fin = '';

    #[Validate('nullable|string|size:3')]
    public string $pais = '';

    #[Validate('nullable|integer|min:1|max:9999')]
    public ?int $edicion = null;

    // ── Tipos disponibles ─────────────────────────────────────────────
    public array $tipos = [
        'etapas' => 'Carrera por etapas',
        'un_dia' => 'Clásica (un día)',
    ];

    // ── Ciclo de vida ────────────────────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingNombre(string $value): void
    {
        if (! $this->editingId) {
            $this->slug = \Illuminate\Support\Str::slug($value);
        }
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
        $prueba = Prueba::findOrFail($id);

        $this->editingId   = $prueba->id;
        $this->nombre      = $prueba->nombre;
        $this->slug        = $prueba->slug;
        $this->tipo        = $prueba->tipo;
        $this->fecha_inicio = $prueba->fecha_inicio->format('Y-m-d');
        $this->fecha_fin    = $prueba->fecha_fin->format('Y-m-d');
        $this->pais        = $prueba->pais ?? '';
        $this->edicion     = $prueba->edicion;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nombre'       => $this->nombre,
            'slug'         => \Illuminate\Support\Str::slug($this->slug),
            'tipo'         => $this->tipo,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin'    => $this->fecha_fin,
            'pais'         => $this->pais ?: null,
            'edicion'      => $this->edicion,
        ];

        if ($this->editingId) {
            Prueba::findOrFail($this->editingId)->update($data);
            Flux::toast(heading: 'Prueba actualizada', text: $this->nombre, variant: 'success');
        } else {
            Prueba::create($data);
            Flux::toast(heading: 'Prueba creada', text: $this->nombre, variant: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function cerrarClasificacion(int $id): void
    {
        $prueba = Prueba::findOrFail($id);
        $total  = (new CerrarClasificacionGeneralAction())->execute($prueba);

        Flux::toast(
            heading: 'Clasificación cerrada',
            text: "{$prueba->nombre} · {$total} ciclistas clasificados",
            variant: 'success'
        );
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // ── Helpers ───────────────────────────────────────────────────────
    private function resetForm(): void
    {
        $this->nombre       = '';
        $this->slug         = '';
        $this->tipo         = 'etapas';
        $this->fecha_inicio = '';
        $this->fecha_fin    = '';
        $this->pais         = '';
        $this->edicion      = null;
        $this->resetValidation();
    }

    #[Computed]
    public function pruebas()
    {
        $pruebas = Prueba::query()
            ->when($this->search, fn ($q) => $q
                ->where('nombre', 'like', "%{$this->search}%")
                ->orWhere('pais', 'like', "%{$this->search}%")
            )
            ->withCount('etapas')
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return $pruebas;
    }

    // ── Render ────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.pruebas');
    }
}
