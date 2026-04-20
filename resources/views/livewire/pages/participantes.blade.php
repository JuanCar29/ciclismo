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
        <flux:button wire:click="create" variant="primary" icon="plus" class="cursor-pointer">
            Inscribir ciclista
        </flux:button>
    </div>

    {{-- Tabla --}}
    <flux:table>
        <flux:table.columns class="bg-zinc-100">
            <flux:table.column align="center" class="w-20">Dorsal</flux:table.column>
            <flux:table.column align="center">Ciclista</flux:table.column>
            <flux:table.column align="center">Equipo</flux:table.column>
            <flux:table.column align="center">Nacionalidad</flux:table.column>
            <flux:table.column align="center">Abandono</flux:table.column>
            <flux:table.column align="center">Acciones</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($participantes as $p)
                <flux:table.row :key="$p->id">
                    <flux:table.cell align="center" class="font-medium tabular-nums w-20">
                        {{ $p->dorsal }}
                    </flux:table.cell>
                    <flux:table.cell class="font-medium">
                        {{ $p->ciclista->apellidos }}, {{ $p->ciclista->nombre }}
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        @if ($p->equipo)
                            <flux:badge>
                                {{ $p->equipo->nombre }}
                            </flux:badge>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        {{ $p->ciclista->nacionalidad ?? '—' }}
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        @if ($p->abandono)
                            <flux:badge color="red" variant="outline">
                                Etapa {{ $p->abandono }}
                            </flux:badge>
                        @else
                            <flux:badge color="green" variant="outline">En carrera</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:button wire:click="edit({{ $p->id }})" variant="filled" size="xs" icon="pencil-square" class="cursor-pointer">
                            Editar
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-zinc-400 py-8">
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

            <flux:select
                wire:model.live="equipo_id"
                label="Equipo en esta prueba"
            >
                <flux:select.option value="">Selecciona un equipo...</flux:select.option>
                @foreach ($this->equipos as $equipo)
                    <flux:select.option value="{{ $equipo['value'] }}">
                        {{ $equipo['label'] }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            {{-- Ciclista: solo en creación --}}
            @if (! $editingId)
                <flux:select
                    wire:model="ciclista_id"
                    label="Ciclista"
                >
                    <flux:select.option value="">Selecciona un ciclista...</flux:select.option>
                    @foreach ($this->ciclistas as $ciclista)
                        <flux:select.option value="{{ $ciclista['value'] }}">
                            {{ $ciclista['label'] }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            @endif

            <flux:input
                wire:model="dorsal"
                label="Dorsal"
                type="number"
                min="1"
                max="999"
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
            <flux:button wire:click="closeModal" variant="ghost" class="cursor-pointer">
                Cancelar
            </flux:button>
            <flux:button wire:click="save" variant="primary" class="cursor-pointer">
                {{ $editingId ? 'Actualizar' : 'Inscribir' }}
            </flux:button>
        </div>
    </flux:modal>

    <flux:toast />
</div>
