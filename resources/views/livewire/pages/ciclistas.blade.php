<div class="max-w-7xl mx-auto p-4 border border-zinc-200 rounded-lg shadow-sm">

    {{-- Cabecera de página --}}
    <flux:heading size="xl">Ciclistas</flux:heading>
    <flux:subheading>Gestión del registro de ciclistas</flux:subheading>

    {{-- Barra de acciones --}}
    <div class="flex items-center justify-between gap-4 mt-6 mb-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar por nombre, apellidos o equipo..."
            icon="magnifying-glass"
            class="max-w-sm"
        />
        <flux:button wire:click="create" variant="primary" icon="plus">
            Nuevo ciclista
        </flux:button>
    </div>

    {{-- Tabla --}}
    <flux:table :paginate="$ciclistas">
        <flux:table.columns class="bg-zinc-100">
            <flux:table.column align="center">Nombre</flux:table.column>
            <flux:table.column align="center">Equipo</flux:table.column>
            <flux:table.column align="center">Nacionalidad</flux:table.column>
            <flux:table.column align="center">F. Nacimiento</flux:table.column>
            <flux:table.column align="center">Acciones</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($ciclistas as $ciclista)
                <flux:table.row :key="$ciclista->id">
                    <flux:table.cell>
                        <flux:text variant="strong" class="ml-4">{{ $ciclista->apellidos }}, {{ $ciclista->nombre }}</flux:text>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        @if ($ciclista->equipo)
                            <flux:badge variant="outline" color="lime" size="sm">{{ $ciclista->equipo->nombre }}</flux:badge>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">{{ $ciclista->nacionalidad ?? '—' }}</flux:table.cell>
                    <flux:table.cell align="center">
                        {{ $ciclista->fecha_nacimiento_formateada }}
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:button wire:click="edit({{ $ciclista->id }})" variant="ghost" size="sm" icon="pencil-square">
                            Editar
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center text-zinc-400 py-8">
                        No se encontraron ciclistas.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal crear / editar --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <flux:heading>
            {{ $editingId ? 'Editar ciclista' : 'Nuevo ciclista' }}
        </flux:heading>

        <div class="space-y-4 mt-4">
            <div class="grid grid-cols-2 gap-4">
                <flux:input
                    wire:model="nombre"
                    label="Nombre"
                    placeholder="Tadej"
                    required
                />
                <flux:input
                    wire:model="apellidos"
                    label="Apellidos"
                    placeholder="Pogačar"
                    required
                />
            </div>

            <flux:select
                wire:model="equipo_id"
                label="Equipo"
                placeholder="Selecciona un equipo..."
            >
                <flux:select.option value="">Sin equipo</flux:select.option>
                @foreach ($this->equipos as $equipo)
                    <flux:select.option value="{{ $equipo['value'] }}">
                        {{ $equipo['label'] }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input
                    wire:model="nacionalidad"
                    label="Nacionalidad (ISO 3)"
                    placeholder="SVN"
                    maxlength="3"
                />
                <flux:input
                    wire:model="fecha_nacimiento"
                    label="Fecha de nacimiento"
                    type="date"
                />
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
