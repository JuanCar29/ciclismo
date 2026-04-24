<?php

namespace App\Livewire\Pages;

use App\Actions\CerrarClasificacionEtapaAction;
use App\Enums\TipoEtapa;
use App\Models\Etapa;
use App\Models\Prueba;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Etapas')]
class Etapas extends Component
{
    use WithPagination;

    // ── Props de contexto ────────────────────────────────────────────
    public int $prueba_id;

    public string $pruebaNombre = '';

    // ── Estado de la UI ──────────────────────────────────────────────
    public bool $showModal = false;

    public ?int $editingId = null;

    // ── Campos del formulario ────────────────────────────────────────
    #[Validate('required|integer|min:1|max:999')]
    public int $numero = 1;

    #[Validate('nullable|string|max:255')]
    public string $nombre = '';

    #[Validate('nullable|string|max:255')]
    public string $salida = '';

    #[Validate('nullable|string|max:255')]
    public string $llegada = '';

    #[Validate('nullable|numeric|min:0|max:999.99')]
    public ?float $distancia_km = null;

    #[Validate]
    public string $tipo = 'llano';

    #[Validate('required|date')]
    public string $fecha = '';

    // ── Tipos disponibles ─────────────────────────────────────────────
    public array $tipos = [];

    // ── Ciclo de vida ────────────────────────────────────────────────
    public function mount(Prueba $prueba): void
    {
        $this->prueba_id = $prueba->id;
        $this->pruebaNombre = $prueba->nombre;
        $this->tipos = TipoEtapa::options();
    }

    protected function rules(): array
    {
        return [
            'tipo' => ['required', Rule::enum(TipoEtapa::class)],
        ];
    }

    // ── Acciones ─────────────────────────────────────────────────────
    public function cerrarClasificacion(int $etapaId, CerrarClasificacionEtapaAction $action): void
    {
        $etapa = Etapa::where('prueba_id', $this->prueba_id)
            ->findOrFail($etapaId);

        $actualizados = $action->execute($etapa);

        Flux::toast(
            heading: 'Clasificación actualizada',
            text: "{$actualizados} ciclistas recalculados",
            variant: 'success'
        );
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;

        // Sugerir el número siguiente disponible
        $ultimo = Etapa::where('prueba_id', $this->prueba_id)->max('numero');
        $this->numero = $ultimo ? $ultimo + 1 : 1;

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $etapa = Etapa::findOrFail($id);

        $this->editingId = $etapa->id;
        $this->numero = $etapa->numero;
        $this->nombre = $etapa->nombre ?? '';
        $this->salida = $etapa->salida ?? '';
        $this->llegada = $etapa->llegada ?? '';
        $this->distancia_km = $etapa->distancia_km;
        $this->tipo = $etapa->tipo->value;
        $this->fecha = Carbon::parse($etapa->fecha)->format('Y-m-d');

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'prueba_id' => $this->prueba_id,
            'numero' => $this->numero,
            'nombre' => $this->nombre ?: $this->salida.' - '.$this->llegada,
            'salida' => $this->salida ?: null,
            'llegada' => $this->llegada ?: null,
            'distancia_km' => $this->distancia_km,
            'tipo' => $this->tipo,
            'fecha' => $this->fecha,
        ];

        if ($this->editingId) {
            Etapa::findOrFail($this->editingId)->update($data);
            Flux::toast(heading: 'Etapa actualizada', text: "Etapa {$this->numero}", variant: 'success');
        } else {
            Etapa::create($data);
            Flux::toast(heading: 'Etapa creada', text: "Etapa {$this->numero}", variant: 'success');
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
        $this->numero = 1;
        $this->nombre = '';
        $this->salida = '';
        $this->llegada = '';
        $this->distancia_km = null;
        $this->tipo = 'llano';
        $this->fecha = '';
        $this->resetValidation();
    }

    // ── Render ────────────────────────────────────────────────────────
    #[Computed]
    public function etapas()
    {
        return Etapa::where('prueba_id', $this->prueba_id)
            ->orderBy('numero')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.pages.etapas');
    }
}
