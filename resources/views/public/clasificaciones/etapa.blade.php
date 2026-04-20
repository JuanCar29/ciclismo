@extends('layouts.public')

@section('title', 'Etapa ' . $etapa->numero . ' — ' . $prueba->nombre)

@section('content')

    {{-- Breadcrumb --}}
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('public.inicio')">Inicio</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('public.pruebas.index')">Pruebas</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('public.pruebas.show', $prueba)">{{ $prueba->nombre }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Etapa {{ $etapa->numero }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Cabecera --}}
    <flux:card class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <x-public.titulos> 
            <x-slot:titulo>
                @if ($prueba->tipo === 'etapas')
                    Etapa {{ $etapa->numero }} {{ $etapa->nombre }}
                @else
                    Clásica {{ $prueba->nombre }} {{ $prueba->edicion }}
                @endif
            </x-slot:titulo>
            <x-slot:subtitulo>
                <div class="flex items-center gap-2">
                    {{ $prueba->nombre }}
                    <flux:separator variant="subtle" vertical />
                    {{ $etapa->fecha->translatedFormat('d F Y') }}
                    <flux:separator variant="subtle" vertical />
                    {{ number_format($etapa->distancia_km, 1, ',', '.') }} km
                    <flux:separator variant="subtle" vertical />
                    {{ $etapa->velocidad_media_ganador }}
                </div>
            </x-slot:subtitulo> 
        </x-public.titulos>

        {{-- Navegación entre etapas --}}
        @if ($etapas->count() > 1)
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                @foreach ($etapas as $e)
                    <flux:button
                        :href="route('public.clasificacion.etapa', [$prueba, $e])"
                        size="sm"
                        :variant="$e->id === $etapa->id ? 'primary' : 'filled'"
                        :color="$e->id === $etapa->id ? 'indigo' : ''"
                    >
                        {{ $e->numero }}
                    </flux:button>
                @endforeach
            </div>
        @endif
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
                    <flux:table.column align="center" class="hidden md:table-cell">Bonif.</flux:table.column>
                    <flux:table.column align="center" class="hidden md:table-cell">Penal.</flux:table.column>
                    <flux:table.column align="center">Tiempo neto</flux:table.column>
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
                            </flux:table.cell>

                            {{-- Equipo --}}
                            <flux:table.cell class="hidden sm:table-cell">
                                <flux:text size="sm" class="text-zinc-500">
                                    {{ $item['equipo']?->abreviatura ?? $item['equipo']?->nombre ?? '—' }}
                                </flux:text>
                            </flux:table.cell>

                            {{-- Dorsal --}}
                            <flux:table.cell align="center" class="hidden sm:table-cell">
                                <flux:text size="sm" class="tabular-nums">{{ $item['dorsal'] ?? '—' }}</flux:text>
                            </flux:table.cell>

                            {{-- Bonificación --}}
                            <flux:table.cell align="center" class="hidden md:table-cell">
                                @if ($item['bonificacion'])
                                    <flux:badge color="green" size="xs" variant="outline">-{{ $item['bonificacion'] }}s</flux:badge>
                                @else
                                    <flux:text size="xs" class="text-zinc-300 dark:text-zinc-600">—</flux:text>
                                @endif
                            </flux:table.cell>

                            {{-- Penalización --}}
                            <flux:table.cell align="center" class="hidden md:table-cell">
                                @if ($item['penalizacion'])
                                    <flux:badge color="red" size="xs" variant="outline">+{{ $item['penalizacion'] }}s</flux:badge>
                                @else
                                    <flux:text size="xs" class="text-zinc-300 dark:text-zinc-600">—</flux:text>
                                @endif
                            </flux:table.cell>

                            {{-- Tiempo neto --}}
                            <flux:table.cell align="center">
                                <flux:text size="xs" class="font-mono tabular-nums font-semibold">{{ $item['formateado'] }}</flux:text>
                            </flux:table.cell>

                            {{-- Diferencia --}}
                            <flux:table.cell align="center" class="hidden sm:table-cell">
                                @if ($item['diferencia'])
                                    <flux:text size="xs" class="font-mono tabular-nums">{{ $item['diferencia'] }}</flux:text>
                                @else
                                    <flux:badge color="amber" size="xs">Ganador</flux:badge>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @else
        <div class="text-center py-24">
            <flux:heading size="lg">No hay tiempos registrados para esta etapa.</flux:heading>
        </div>
    @endif

@endsection
