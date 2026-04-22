@extends('layouts.public', ['title' => 'Clasificación general — ' . $prueba->nombre])

@section('content')

    {{-- Breadcrumb --}}
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('public.inicio')">Inicio</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('public.pruebas.index')">Pruebas</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('public.pruebas.show', $prueba)">{{ $prueba->nombre }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Clasificación general</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Cabecera --}}
    <flux:card class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <x-public.titulos> 
            <x-slot:titulo>Clasificación general</x-slot:titulo>
            <x-slot:subtitulo>
                <div class="flex items-center gap-2">
                    {{ $prueba->nombre }}
                    @if ($prueba->edicion) ({{ $prueba->edicion }}) @endif
                    <flux:separator variant="subtle" vertical />
                    {{ $etapas->count() }} etapa{{ $etapas->count() !== 1 ? 's' : '' }}
                </div>
            </x-slot:subtitulo>
        </x-public.titulos>

        {{-- Navegación clasificaciones --}}
        <div class="flex items-center gap-2">
            <flux:button variant="primary" size="sm" color="indigo">General</flux:button>
            <flux:button :href="route('public.clasificacion.puntos', $prueba)" size="sm">Puntos</flux:button>
            <flux:button :href="route('public.clasificacion.equipos', $prueba)" size="sm">Equipos</flux:button>
        </div>
    </flux:card>

    {{-- Tabla --}}
    @if ($clasificacion->isNotEmpty())
        <flux:card class="p-4">
            <flux:table container:class="max-h-200">
                <flux:table.columns sticky class="bg-zinc-100">
                    <flux:table.column align="center" class="w-16">Pos.</flux:table.column>
                    <flux:table.column>Ciclista</flux:table.column>
                    <flux:table.column class="hidden sm:table-cell">Equipo</flux:table.column>
                    <flux:table.column align="center" class="w-12 hidden sm:table-cell">Dor.</flux:table.column>
                    <flux:table.column align="center" class="hidden md:table-cell">Etapas</flux:table.column>
                    <flux:table.column align="center">Tiempo</flux:table.column>
                    <flux:table.column align="center" class="hidden sm:table-cell">Dif.</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($clasificacion as $item)
                        <flux:table.row key="{{ $item['ciclista']->id }}">

                            {{-- Posición --}}
                            <flux:table.cell align="center">
                                @if ($item['posicion'] === 1)
                                    <flux:badge color="yellow">1</flux:badge>
                                @elseif ($item['posicion'] === 2)
                                    <flux:badge color="zinc">2</flux:badge>
                                @elseif ($item['posicion'] === 3)
                                    <flux:badge color="orange">3</flux:badge>
                                @else
                                    <flux:text class="tabular-nums">{{ $item['posicion'] }}</flux:text>
                                @endif
                            </flux:table.cell>

                            {{-- Ciclista --}}
                            <flux:table.cell>
                                <flux:link :href="route('public.ciclistas.show', $item['ciclista'])" variant="subtle">
                                    {{ $item['ciclista']->apellidos }}, {{ $item['ciclista']->nombre }}
                                </flux:link>
                                @if ($item['abandono'])
                                    <flux:badge color="red" size="sm" class="ml-2">ABD et.{{ $item['abandono'] }}</flux:badge>
                                @endif
                            </flux:table.cell>

                            {{-- Equipo --}}
                            <flux:table.cell class="hidden sm:table-cell">
                                <flux:text>
                                    {{ $item['equipo']?->nombre ?? '-' }}
                                </flux:text>
                            </flux:table.cell>

                            {{-- Dorsal --}}
                            <flux:table.cell align="center" class="hidden sm:table-cell">
                                <flux:text size="sm" class="tabular-nums">{{ $item['dorsal'] ?? '—' }}</flux:text>
                            </flux:table.cell>

                            {{-- Etapas --}}
                            <flux:table.cell align="center" class="hidden md:table-cell">
                                <flux:text size="sm" class="tabular-nums">{{ $item['etapas'] }}</flux:text>
                            </flux:table.cell>

                            {{-- Tiempo --}}
                            <flux:table.cell align="center">
                                <flux:text size="xs" class="font-mono tabular-nums">{{ $item['formateado'] }}</flux:text>
                            </flux:table.cell>

                            {{-- Diferencia --}}
                            <flux:table.cell align="center" class="hidden sm:table-cell">
                                @if ($item['diferencia'])
                                    <flux:text size="xs" class="font-mono tabular-nums">{{ $item['diferencia'] }}</flux:text>
                                @else
                                    <flux:badge color="amber" size="sm">Líder</flux:badge>
                                @endif
                            </flux:cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @else
        <div class="text-center py-24">
            <flux:heading size="lg">No hay tiempos registrados todavía para esta prueba.</flux:heading>
        </div>
    @endif

@endsection
