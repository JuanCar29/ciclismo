<div class="max-w-7xl mx-auto p-4 border border-zinc-200 rounded-lg shadow-sm">

    {{-- Cabecera de página --}}
    <flux:heading size="xl">Pruebas</flux:heading>
    <flux:subheading>Gestión de carreras y pruebas ciclistas</flux:subheading>

    {{-- Barra de acciones --}}
    <div class="flex items-center justify-between gap-4 mt-6 mb-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o país..."
            icon="magnifying-glass" class="max-w-sm" />
        <flux:button wire:click="create" variant="primary" icon="plus">
            Nueva prueba
        </flux:button>
    </div>

    {{-- Tabla --}}
    <flux:table :paginate="$this->pruebas">
        <flux:table.columns class="bg-zinc-100">
            <flux:table.column align="center">Nombre</flux:table.column>
            <flux:table.column align="center">Tipo</flux:table.column>
            <flux:table.column align="center">Fecha inicio</flux:table.column>
            <flux:table.column align="center">Fecha fin</flux:table.column>
            <flux:table.column align="center">Etapas</flux:table.column>
            <flux:table.column align="center">Acciones</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->pruebas as $prueba)
                <flux:table.row :key="$prueba->id">
                    <flux:table.cell class="font-medium" align="center">
                        {{ $prueba->nombre }}
                        @if ($prueba->edicion)
                            <span class="text-zinc-400 text-sm font-normal">({{ $prueba->edicion }}ª ed.)</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        @if ($prueba->tipo === 'etapas')
                            <flux:badge color="blue" variant="outline">Por etapas</flux:badge>
                        @else
                            <flux:badge color="amber" variant="outline">Clásica</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        {{ \Carbon\Carbon::parse($prueba->fecha_inicio)->format('d/m/Y') }}
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        {{ \Carbon\Carbon::parse($prueba->fecha_fin)->format('d/m/Y') }}
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge variant="outline">{{ $prueba->etapas_count }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="flex justify-center gap-2">
                        <flux:button wire:click="edit({{ $prueba->id }})" variant="ghost" size="sm"
                            icon="pencil-square">
                            Editar
                        </flux:button>
                        <flux:button href="{{ route('etapas', $prueba->id) }}" variant="filled" size="sm"
                            icon="list-bullet">
                            Etapas
                        </flux:button>
                        <flux:button href="{{ route('participantes', $prueba->id) }}" variant="filled" size="sm"
                            icon="users">
                            Participantes
                        </flux:button>
                        <flux:button wire:click="cerrarClasificacion({{ $prueba->id }})"
                            wire:confirm="¿Cerrar y guardar la clasificación general de {{ $prueba->nombre }}?"
                            size="sm" icon="flag">
                            Cerrar CG
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-zinc-400 py-8">
                        No se encontraron pruebas.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal crear / editar --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <flux:heading>
            {{ $editingId ? 'Editar prueba' : 'Nueva prueba' }}
        </flux:heading>

        <div class="space-y-4 mt-4">
            <flux:input wire:model="nombre" label="Nombre" placeholder="Vuelta a España" required />
            <flux:input wire:model="slug" label="Slug" placeholder="vuelta-a-espana"
                description="Se genera automáticamente al escribir el nombre." />

            <flux:select wire:model="tipo" label="Tipo">
                @foreach ($tipos as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="fecha_inicio" label="Fecha inicio" type="date" required />
                <flux:input wire:model="fecha_fin" label="Fecha fin" type="date" required />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="pais" label="País (ISO 3)" placeholder="ESP" maxlength="3" />
                <flux:input wire:model="edicion" label="Edición" type="number" placeholder="80" min="1" />
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="closeModal" variant="ghost">
                Cancelar
            </flux:button>
            <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">
                    {{ $editingId ? 'Actualizar' : 'Guardar' }}
                </span>
                <span wire:loading wire:target="save">Guardando...</span>
            </flux:button>
        </div>
    </flux:modal>

    <flux:toast />
</div>
