<div class="max-w-7xl mx-auto p-4 border border-zinc-200 rounded-lg shadow-sm">

    {{-- Cabecera de página --}}
    <div class="flex items-center gap-2 mb-1">
        <flux:button href="{{ route('pruebas') }}" variant="ghost" size="sm" icon="arrow-left" />
        <flux:heading size="xl">Participantes</flux:heading>
    </div>
    <flux:subheading>{{ $pruebaNombre }}</flux:subheading>

    {{-- Barra de acciones --}}
    <div class="flex items-center justify-between gap-4 mt-6 mb-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar por nombre o apellidos..."
            icon="magnifying-glass"
            class="max-w-sm"
        />
        <flux:button wire:click="create" variant="primary" icon="plus">
            Inscribir ciclista
        </flux:button>
    </div>

    {{-- Tabla --}}
    <flux:table>
        <flux:table.columns class="bg-zinc-100">
            <flux:table.column align="center">Dorsal</flux:table.column>
            <flux:table.column align="center">Apellidos</flux:table.column>
            <flux:table.column align="center">Nombre</flux:table.column>
            <flux:table.column align="center">Equipo</flux:table.column>
            <flux:table.column align="center">Nacionalidad</flux:table.column>
            <flux:table.column align="center">Abandono</flux:table.column>
            <flux:table.column align="center">Acciones</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($participantes as $p)
                <flux:table.row :key="$p->id">
                    <flux:table.cell class="font-medium tabular-nums w-16">
                        {{ $p->dorsal }}
                    </flux:table.cell>
                    <flux:table.cell class="font-medium">
                        {{ $p->ciclista->apellidos }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $p->ciclista->nombre }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($p->equipo)
                            <flux:badge variant="outline">
                                {{ $p->equipo->abreviatura ?? $p->equipo->nombre }}
                            </flux:badge>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $p->ciclista->nacionalidad ?? '—' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($p->abandono)
                            <flux:badge color="red" variant="outline">
                                Etapa {{ $p->abandono }}
                            </flux:badge>
                        @else
                            <flux:badge color="green" variant="outline">En carrera</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button wire:click="edit({{ $p->id }})" variant="ghost" size="sm" icon="pencil-square">
                            Editar
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center text-zinc-400 py-8">
                        No hay ciclistas inscritos en esta prueba.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Resumen --}}
    @if ($participantes->isNotEmpty())
        <div class="mt-3 text-sm text-zinc-400">
            {{ $participantes->count() }} ciclista{{ $participantes->count() !== 1 ? 's' : '' }} inscritos
            &middot;
            {{ $participantes->whereNotNull('abandono')->count() }} abandono{{ $participantes->whereNotNull('abandono')->count() !== 1 ? 's' : '' }}
        </div>
    @endif

    {{-- Modal inscribir / editar --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <flux:heading>
            {{ $editingId ? 'Editar participante' : 'Inscribir ciclista' }}
        </flux:heading>

        <div class="space-y-4 mt-4">

            {{-- Ciclista: solo en creación --}}
            @if (! $editingId)
                <flux:select
                    wire:model.live="ciclista_id"
                    label="Ciclista"
                    placeholder="Selecciona un ciclista..."
                    required
                >
                    @foreach ($this->ciclistas as $ciclista)
                        <flux:select.option value="{{ $ciclista['value'] }}">
                            {{ $ciclista['label'] }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            @endif

            <flux:select
                wire:model="equipo_id"
                label="Equipo en esta prueba"
                placeholder="Selecciona un equipo..."
                required
            >
                @foreach ($this->equipos as $equipo)
                    <flux:select.option value="{{ $equipo['value'] }}">
                        {{ $equipo['label'] }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input
                wire:model="dorsal"
                label="Dorsal"
                type="number"
                min="1"
                max="999"
                required
            />

            {{-- Abandono: solo en edición --}}
            @if ($editingId)
                <flux:input
                    wire:model="abandono"
                    label="Abandono (nº de etapa)"
                    type="number"
                    min="1"
                    placeholder="Dejar vacío si sigue en carrera"
                    description="Introduce el número de etapa en que abandonó, o deja vacío."
                />
            @endif

        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="closeModal" variant="ghost">
                Cancelar
            </flux:button>
            <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">
                    {{ $editingId ? 'Actualizar' : 'Inscribir' }}
                </span>
                <span wire:loading wire:target="save">Guardando...</span>
            </flux:button>
        </div>
    </flux:modal>

    <flux:toast />
</div>
