<div class="max-w-7xl mx-auto p-4 border border-zinc-200 rounded-lg shadow-sm">

    {{-- Cabecera de página --}}
    <div class="flex items-center gap-2 mb-1">
        <flux:button href="{{ route('pruebas') }}" variant="ghost" size="sm" icon="arrow-left" />
        <flux:heading size="xl">Etapas</flux:heading>
    </div>
    <flux:subheading>{{ $pruebaNombre }}</flux:subheading>

    {{-- Barra de acciones --}}
    <div class="flex justify-end mt-6 mb-4">
        <flux:button wire:click="create" variant="primary" icon="plus" class="cursor-pointer">
            Nueva etapa
        </flux:button>
    </div>

    {{-- Tabla --}}
    <flux:table>
        <flux:table.columns class="bg-zinc-100">
            <flux:table.column class="w-20" align="center">Nº</flux:table.column>
            <flux:table.column align="center">Nombre</flux:table.column>
            <flux:table.column align="center">Salida</flux:table.column>
            <flux:table.column align="center">Llegada</flux:table.column>
            <flux:table.column align="center">Distancia</flux:table.column>
            <flux:table.column align="center">Tipo</flux:table.column>
            <flux:table.column align="center">Fecha</flux:table.column>
            <flux:table.column align="center">Acciones</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->etapas as $etapa)
                <flux:table.row :key="$etapa->id">
                    <flux:table.cell align="center" class="font-medium tabular-nums">
                        {{ $etapa->numero }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $etapa->nombre ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $etapa->salida ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $etapa->llegada ?? '—' }}</flux:table.cell>
                    <flux:table.cell class="tabular-nums">
                        @if ($etapa->distancia_km)
                            {{ number_format($etapa->distancia_km, 1, ',', '.') }} km
                        @else
                            —
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge color="{{ $etapa->tipo->badgeColor() }}" variant="outline">
                            {{ $etapa->tipo->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        {{ \Carbon\Carbon::parse($etapa->fecha)->format('d/m/Y') }}
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:button wire:click="edit({{ $etapa->id }})" variant="ghost" size="sm"
                            icon="pencil-square" class="cursor-pointer">
                            Editar
                        </flux:button>
                        <flux:button href="{{ route('tiempos', [$prueba_id, $etapa->id]) }}" variant="filled"
                            size="sm" icon="clock" class="cursor-pointer">
                            Tiempos
                        </flux:button>
                        <flux:button wire:click="cerrarClasificacion({{ $etapa->id }})" size="sm"
                            icon="flag" class="cursor-pointer">
                            Cerrar CE
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center text-zinc-400 py-8">
                        No hay etapas registradas para esta prueba.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal crear / editar --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <flux:heading>
            {{ $editingId ? 'Editar etapa' : 'Nueva etapa' }}
        </flux:heading>

        <div class="space-y-4 mt-4">
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="numero" label="Número de etapa" type="number" min="1" required />
                <flux:input wire:model="fecha" label="Fecha" type="date" required />
            </div>

            <flux:input wire:model="nombre" label="Nombre" placeholder="Bilbao - Jaizkibel" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="salida" label="Salida" placeholder="Bilbao" />
                <flux:input wire:model="llegada" label="Llegada" placeholder="Jaizkibel" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="distancia_km" label="Distancia (km)" type="number" step="0.1"
                    min="0" placeholder="187.5" />
                <flux:select wire:model="tipo" label="Tipo">
                    @foreach ($tipos as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
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

    <flux:toast position="top-end" />
</div>
