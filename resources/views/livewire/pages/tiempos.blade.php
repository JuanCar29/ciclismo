<div class="max-w-7xl mx-auto p-4 border border-zinc-200 rounded-lg shadow-sm">

    {{-- Cabecera --}}
    <div class="flex items-center gap-2 mb-1">
        <flux:button href="{{ route('pruebas') }}" variant="ghost" size="sm" icon="arrow-left" />
        <flux:heading size="xl">Tiempos</flux:heading>
    </div>
    <flux:subheading>
        {{ $pruebaNombre }} · Etapa {{ $etapaNumero }}{{ $etapaNombre ? ' — ' . $etapaNombre : '' }}
    </flux:subheading>

    @if ($etapa_id)
        {{-- Formulario rápido de entrada --}}
        <div class="mt-6 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:heading size="sm" class="mb-4">Registrar tiempo</flux:heading>

            <div class="flex flex-wrap items-end gap-3">
                {{-- Dorsal --}}
                <div class="w-24">
                    <flux:input
                        wire:model="dorsal"
                        label="Dorsal"
                        type="number"
                        min="1"
                        max="999"
                        placeholder="1"
                        wire:keydown.enter="registrar"
                    />
                </div>

                {{-- Tiempo --}}
                <div class="w-20">
                    <flux:input
                        wire:model="horas"
                        label="Horas"
                        type="number"
                        min="0"
                        max="23"
                        placeholder="0"
                    />
                </div>
                <div class="flex items-end pb-2 text-zinc-400 font-mono text-lg">:</div>
                <div class="w-20">
                    <flux:input
                        wire:model="minutos"
                        label="Minutos"
                        type="number"
                        min="0"
                        max="59"
                        placeholder="00"
                    />
                </div>
                <div class="flex items-end pb-2 text-zinc-400 font-mono text-lg">:</div>
                <div class="w-20">
                    <flux:input
                        wire:model="segundos"
                        label="Segundos"
                        type="number"
                        min="0"
                        max="59"
                        placeholder="00"
                    />
                </div>

                {{-- Separador --}}
                <div class="flex items-end pb-2 text-zinc-300 dark:text-zinc-600 text-lg px-1">|</div>

                {{-- Bonificación y penalización --}}
                <div class="w-24">
                    <flux:input
                        wire:model="bonificacion"
                        label="Bonif. (s)"
                        type="number"
                        min="0"
                        placeholder="0"
                    />
                </div>
                <div class="w-24">
                    <flux:input
                        wire:model="penalizacion"
                        label="Penal. (s)"
                        type="number"
                        min="0"
                        placeholder="0"
                    />
                </div>
                <div class="w-24">
                    <flux:input
                        wire:model="puntos"
                        label="Puntos"
                        type="number"
                        min="0"
                        placeholder="0"
                    />
                </div>

                {{-- Botón --}}
                <flux:button
                    wire:click="registrar"
                    variant="primary"
                    icon="plus"
                    wire:loading.attr="disabled"
                    wire:target="registrar"
                    class="mb-px"
                >
                    Añadir
                </flux:button>
            </div>

            {{-- Hint --}}
            <p class="text-xs text-zinc-400 mt-2">
                Los campos h/m/s se mantienen entre registros. Pulsa Enter en el dorsal para registrar rápido.
            </p>
        </div>

        {{-- Tabla de tiempos --}}
        <div class="mt-6">
            <div class="flex items-center justify-between mb-3">
                <flux:heading size="sm">
                    Tiempos registrados
                    @if ($this->tiempos->isNotEmpty())
                        <flux:badge variant="outline" class="ml-2">{{ $this->tiempos->count() }}</flux:badge>
                    @endif
                </flux:heading>
            </div>

            <flux:table container:class="max-h-200">
                <flux:table.columns class="bg-zinc-100" sticky>
                    <flux:table.column align="center" class="w-20">Pos.</flux:table.column>
                    <flux:table.column align="center" class="w-20">Dorsal</flux:table.column>
                    <flux:table.column align="center">Ciclista</flux:table.column>
                    <flux:table.column align="center">Tiempo</flux:table.column>
                    <flux:table.column align="center">Bonif.</flux:table.column>
                    <flux:table.column align="center">Penal.</flux:table.column>
                    <flux:table.column align="center">Neto</flux:table.column>
                    <flux:table.column align="center">Puntos</flux:table.column>
                    <flux:table.column align="center">Acciones</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->tiempos as $index => $tiempo)
                        @php
                            $participante = \App\Models\CiclistaPrueba::where('prueba_id', $prueba_id)
                                ->where('ciclista_id', $tiempo->ciclista_id)
                                ->first();
                        @endphp
                        <flux:table.row :key="$tiempo->id">
                            <flux:table.cell align="center" class="tabular-nums text-zinc-400 w-20">
                                {{ $index + 1 }}
                            </flux:table.cell>
                            <flux:table.cell align="center" class="tabular-nums font-medium w-20">
                                {{ $participante?->dorsal ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">
                                {{ $tiempo->ciclista->apellidos }}, {{ $tiempo->ciclista->nombre }}
                            </flux:table.cell>
                            <flux:table.cell align="center" class="tabular-nums font-mono">
                                {{ $this->formatearTiempo($tiempo->segundos) }}
                            </flux:table.cell>
                            <flux:table.cell align="center" class="tabular-nums">
                                @if ($tiempo->bonificacion)
                                    <flux:badge color="green" variant="outline">-{{ $tiempo->bonificacion }}s</flux:badge>
                                @else
                                    <span class="text-zinc-300">—</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="center" class="tabular-nums">
                                @if ($tiempo->penalizacion)
                                    <flux:badge color="red" variant="outline">+{{ $tiempo->penalizacion }}s</flux:badge>
                                @else
                                    <span class="text-zinc-300">—</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="center" class="tabular-nums font-mono font-medium">
                                {{ $this->formatearTiempo($tiempo->tiempoNeto()) }}
                            </flux:table.cell>
                            <flux:table.cell align="center" class="tabular-nums">
                                {{ $tiempo->puntos ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="flex justify-center gap-2">
                                <flux:button wire:click="edit({{ $tiempo->id }})" variant="filled" size="sm" icon="pencil-square" class="cursor-pointer" />
                                <flux:button wire:click="delete({{ $tiempo->id }})" variant="danger" size="sm" icon="trash" wire:confirm="¿Eliminar este tiempo?" class="cursor-pointer" />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center text-zinc-400 py-8">
                                No hay tiempos registrados para esta etapa.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    @else
        <div class="mt-8 text-center text-zinc-400 py-12">
            Selecciona una etapa para empezar a registrar tiempos.
        </div>
    @endif

    {{-- Modal edición --}}
    <flux:modal wire:model="showModal" class="w-full max-w-sm">
        <flux:heading>Editar tiempo</flux:heading>

        <div class="space-y-4 mt-4">
            <div class="flex items-end gap-2">
                <div class="w-20">
                    <flux:input wire:model="edit_horas" label="Horas" type="number" min="0" max="23" />
                </div>
                <div class="flex items-end pb-2 text-zinc-400 font-mono text-lg">:</div>
                <div class="w-20">
                    <flux:input wire:model="edit_minutos" label="Minutos" type="number" min="0" max="59" />
                </div>
                <div class="flex items-end pb-2 text-zinc-400 font-mono text-lg">:</div>
                <div class="w-20">
                    <flux:input wire:model="edit_segundos" label="Segundos" type="number" min="0" max="59" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="edit_bonificacion" label="Bonificación (s)" type="number" min="0" />
                <flux:input wire:model="edit_penalizacion" label="Penalización (s)" type="number" min="0" />
                <flux:input wire:model="edit_puntos" label="Puntos" type="number" min="0" />
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="closeModal" variant="ghost">Cancelar</flux:button>
            <flux:button wire:click="update" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="update">Actualizar</span>
                <span wire:loading wire:target="update">Guardando...</span>
            </flux:button>
        </div>
    </flux:modal>

    <flux:toast />
</div>
