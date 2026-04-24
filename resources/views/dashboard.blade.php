<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="flex h-full flex-col items-center justify-center text-center">
                    <flux:heading size="2xl" accent>Pruebas</flux:heading>
                    <flux:text size="xl">
                        {{ $pruebas }}
                    </flux:text>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="flex h-full flex-col items-center justify-center text-center">
                    <flux:heading size="2xl" accent>Ciclistas</flux:heading>
                    <flux:text size="xl">
                        {{ $ciclistas }}
                    </flux:text>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="flex h-full flex-col items-center justify-center text-center">
                    <flux:heading size="2xl" accent>Equipos</flux:heading>
                    <flux:text size="xl">
                        {{ $equipos }}
                    </flux:text>
                </div>
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="mx-auto w-full max-w-6xl p-4 mb-4">
                <flux:heading size="2xl" accent class="mb-4">Últimas etapas</flux:heading>
                <flux:table>
                    <flux:table.columns class="bg-zinc-100">
                        <flux:table.column align="center">Nº</flux:table.column>
                        <flux:table.column align="center">Prueba</flux:table.column>
                        <flux:table.column align="center">Salida</flux:table.column>
                        <flux:table.column align="center">Llegada</flux:table.column>
                        <flux:table.column align="center">Fecha</flux:table.column>
                        <flux:table.column align="center">Distancia</flux:table.column>
                        <flux:table.column align="center">Tipo</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($ultimasEtapas as $etapa)
                            <flux:table.row :key="$etapa->id">
                                <flux:table.cell align="center">{{ $etapa->numero }}</flux:table.cell>
                                <flux:table.cell align="center">{{ $etapa->prueba->nombre }}</flux:table.cell>
                                <flux:table.cell align="center">{{ $etapa->salida }}</flux:table.cell>
                                <flux:table.cell align="center">{{ $etapa->llegada }}</flux:table.cell>
                                <flux:table.cell align="center">{{ $etapa->fecha->format('d/m/Y') }}</flux:table.cell>
                                <flux:table.cell align="center">{{ number_format($etapa->distancia_km, 1, ',', '.') }} km</flux:table.cell>
                                <flux:table.cell align="center">
                                    <flux:badge color="{{ $etapa->tipo->badgeColor() }}" size="sm">{{ $etapa->tipo->label() }}</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7" align="center">No hay etapas registradas</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>
</x-layouts::app>
