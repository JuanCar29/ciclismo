<div class="max-w-7xl mx-auto p-4 border border-zinc-200 rounded-lg shadow-sm">
    {{-- Cabecera de página --}}
    <flux:heading size="xl">Equipos</flux:heading>
    <flux:subheading>Gestión de equipos ciclistas</flux:subheading>

    {{-- Barra de acciones --}}
    <div class="flex items-center justify-between gap-4 mt-6 mb-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar por nombre o abreviatura..."
            icon="magnifying-glass"
            class="max-w-sm"
        />
        <flux:button wire:click="create" variant="primary" icon="plus">
            Nuevo equipo
        </flux:button>
    </div>

    {{-- Tabla --}}
    <flux:table :paginate="$equipos">
        <flux:table.columns class="bg-zinc-100">
            <flux:table.column>Nombre</flux:table.column>
            <flux:table.column>Abreviatura</flux:table.column>
            <flux:table.column>País</flux:table.column>
            <flux:table.column>Web</flux:table.column>
            <flux:table.column>Ciclistas</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($equipos as $equipo)
                <flux:table.row :key="$equipo->id">
                    <flux:table.cell class="font-medium">{{ $equipo->nombre }}</flux:table.cell>
                    <flux:table.cell>{{ $equipo->abreviatura ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $equipo->pais ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($equipo->web)
                            <a href="{{ $equipo->web }}" target="_blank" class="text-blue-600 hover:underline text-sm">
                                {{ parse_url($equipo->web, PHP_URL_HOST) }}
                            </a>
                        @else
                            —
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="outline">{{ $equipo->ciclistas_count }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button wire:click="edit({{ $equipo->id }})" variant="ghost" size="sm" icon="pencil-square">
                            Editar
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-zinc-400 py-8">
                        No se encontraron equipos.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal crear / editar --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <flux:heading>
            {{ $editingId ? 'Editar equipo' : 'Nuevo equipo' }}
        </flux:heading>

        <div class="space-y-4 mt-4">
            <flux:input
                wire:model="nombre"
                label="Nombre"
                placeholder="UAE Team Emirates"
                required
            />
            <div class="grid grid-cols-2 gap-4">
                <flux:input
                    wire:model="abreviatura"
                    label="Abreviatura"
                    placeholder="UAE"
                    maxlength="10"
                />
                <flux:input
                    wire:model="pais"
                    label="País (ISO 3)"
                    placeholder="ESP"
                    maxlength="3"
                />
            </div>
            <flux:input
                wire:model="web"
                label="Web"
                placeholder="https://www.uae-teamemirates.com"
                type="url"
            />
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
