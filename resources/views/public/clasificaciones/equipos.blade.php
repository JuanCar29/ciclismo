@extends('layouts.public', ['title' => 'Clasificación equipos — ' . $prueba->nombre])

@section('content')

    {{-- Breadcrumb --}}
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('public.inicio')">Inicio</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('public.pruebas.index')">Pruebas</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('public.pruebas.show', $prueba)">{{ $prueba->nombre }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Clasificación equipos</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Cabecera --}}
    <flux:card class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <x-public.titulos titulo="Clasificación por equipos" :subtitulo="$prueba->nombre . ($prueba->edicion ? ' (' . $prueba->edicion . ')' : '')" />

        {{-- Navegación clasificaciones --}}
        <div class="flex items-center gap-2">
            <flux:button :href="route('public.clasificacion.general', $prueba)" size="sm">General</flux:button>
            <flux:button :href="route('public.clasificacion.puntos', $prueba)" size="sm">Puntos</flux:button>
            <flux:button variant="primary" size="sm" color="amber">Equipos</flux:button>
        </div>
    </flux:card>

    {{-- Tabla --}}
    @if ($clasificacion->isNotEmpty())
        <flux:card class="p-4">
            <flux:table>
                <flux:table.columns class="bg-zinc-100">
                    <flux:table.column align="center" class="w-20">Pos.</flux:column>
                    <flux:table.column align="center">Equipo</flux:table.column>
                    <flux:table.column align="center" class="hidden sm:table-cell">Corredores</flux:table.column>
                    <flux:table.column align="center">Tiempo</flux:table.column>
                    <flux:table.column align="center" class="hidden sm:table-cell">Dif.</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($clasificacion as $item)
                        <flux:table.row key="{{ $item['equipo']->id }}">

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

                            {{-- Equipo --}}
                            <flux:table.cell>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $item['equipo']?->nombre ?? '—' }}
                                </div>
                                @if ($item['equipo']?->pais)
                                    <flux:text size="xs">{{ $item['equipo']->pais }}</flux:text>
                                @endif
                            </flux:table.cell>

                            {{-- Corredores puntuables --}}
                            <flux:table.cell align="center" class="hidden sm:table-cell">
                                <flux:text class="tabular-nums">{{ $item['corredores'] }}</flux:text>
                            </flux:table.cell>

                            {{-- Tiempo --}}
                            <flux:table.cell align="center">
                                <flux:text size="xs" class="font-mono tabular-nums">{{ $item['formateado'] }}</flux:text>
                            </flux:table.cell>

                            {{-- Diferencia --}}
                            <flux:table.cell align="center" class="hidden sm:table-cell">
                                @if ($item['diferencia'])
                                    <flux:text class="font-mono tabular-nums" size="xs">{{ $item['diferencia'] }}</flux:text>
                                @else
                                    <flux:badge color="amber" size="xs">Líder</flux:badge>
                                @endif
                            </flux:table.cell>
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
