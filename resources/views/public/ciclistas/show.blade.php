@extends('layouts.public')

@section('title', $ciclista->apellidos . ', ' . $ciclista->nombre . ' — Resultados Ciclismo')

@section('content')

    {{-- Breadcrumb --}}
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('public.inicio') }}">Inicio</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('public.ciclistas.index') }}">Ciclistas</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $ciclista->apellidos }}, {{ $ciclista->nombre }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Perfil --}}
    <flux:card class="mb-8 flex flex-col sm:flex-row sm:items-end gap-6">
        <flux:avatar circle size="xl" color="indigo">
            {{ mb_substr(strtoupper($ciclista->nombre), 0, 1) }}{{ mb_substr(strtoupper($ciclista->apellidos), 0, 1) }}
        </flux:avatar>

        <div class="flex-1">
            <x-public.titulos> 
                <x-slot:titulo>{{ $ciclista->nombre }} {{ $ciclista->apellidos }}</x-slot:titulo>
                <x-slot:subtitulo>
                    <div class="flex items-center gap-2">
                        {{ $ciclista->equipo->nombre }}
                        <flux:separator variant="subtle" vertical />
                        {{ $ciclista->nacionalidad }}
                        <flux:separator variant="subtle" vertical />
                        {{ $ciclista->fecha_nacimiento_formateada }}
                        <flux:separator variant="subtle" vertical />
                        {{ $ciclista->edad }}
                    </div>
                </x-slot:subtitulo>
            </x-public.titulos>
        </div>

        <div class="flex gap-6 text-center shrink-0">
            <div>
                <flux:heading size="xl" accent>{{ $participaciones->count() }}</flux:heading>
                <flux:text size="xs">Pruebas</flux:text>
            </div>
            <div>
                <flux:heading size="xl" accent>{{ $participaciones->sum('etapas') }}</flux:heading>
                <flux:text size="xs">Etapas</flux:text>
            </div>
            <div>
                <flux:heading size="xl" accent>{{ $participaciones->whereNotNull('abandono')->count() }}
                </flux:heading>
                <flux:text size="xs">Abandonos</flux:text>
            </div>
        </div>
    </flux:card>

    {{-- Historial de participaciones --}}
    <flux:heading size="xl" accent class="mb-4">Historial de participaciones</flux:heading>

    @if ($participaciones->isNotEmpty())
        <flux:card class="p-4">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Prueba</flux:table.column>
                    <flux:table.column align="center">Dorsal</flux:table.column>
                    <flux:table.column align="center">Equipo</flux:table.column>
                    <flux:table.column align="center">Etapas</flux:table.column>
                    <flux:table.column align="center">Posición</flux:table.column>
                    <flux:table.column align="center">Tiempo total</flux:table.column>
                    <flux:table.column align="center">Estado</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($participaciones as $p)
                        <flux:table.row :key="$p['prueba']->id">
                            <flux:table.cell>
                                <a href="{{ route('public.pruebas.show', $p['prueba']) }}"
                                    class="font-medium text-zinc-900 dark:text-zinc-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    {{ $p['prueba']->nombre }}
                                </a>
                                <div class="text-xs text-zinc-400 mt-0.5">
                                    {{ $p['prueba']->edicion }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <span class="text-zinc-400 tabular-nums">{{ $p['dorsal'] ?? '—' }}</span>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <flux:badge>{{ $p['equipo_abreviatura'] }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <span class="text-zinc-400 tabular-nums">{{ $p['etapas'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <span class="text-zinc-400 tabular-nums">{{ $p['posicion'] ?? '—' }}</span>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <span class="font-mono font-medium text-zinc-900 dark:text-zinc-100 tabular-nums">
                                    {{ $p['formateado'] }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if ($p['abandono'])
                                    <flux:badge color="red">ABD et.{{ $p['abandono'] }}</flux:badge>
                                @else
                                    <flux:badge color="green">Finalizado</flux:badge>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @else
        <flux:heading size="lg" class="my-8 flex justify-center">
            Este ciclista no tiene participaciones registradas todavía.
        </flux:heading>
    @endif

    {{-- Etapas concluidas --}}
    <flux:heading size="xl" accent class="mb-4 mt-12">Etapas concluidas</flux:heading>

    @if ($etapasConcluidas->isNotEmpty())
        <flux:card class="p-4">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Etapa</flux:table.column>
                    <flux:table.column>Prueba</flux:table.column>
                    <flux:table.column align="center">Fecha</flux:table.column>
                    <flux:table.column align="center">Tiempo neto</flux:table.column>
                    <flux:table.column align="center">Posición</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($etapasConcluidas as $e)
                        <flux:table.row :key="$e['etapa']->id">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $e['etapa']->nombre ?? 'Etapa ' . $e['etapa']->numero }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <a href="{{ route('public.pruebas.show', $e['prueba']) }}"
                                    class="text-zinc-600 dark:text-zinc-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    {{ $e['prueba']->nombre }}
                                </a>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <span class="text-zinc-400">{{ $e['fecha']->format('d/m/Y') }}</span>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <span class="font-mono font-medium text-zinc-900 dark:text-zinc-100 tabular-nums">
                                    {{ $e['formateado'] }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if ($e['posicion'] > 3)
                                    <flux:badge>{{ $e['posicion'] }}</flux:badge>
                                @else
                                    <flux:badge color="green">{{ $e['posicion'] }}</flux:badge>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @else
        <flux:heading size="lg" class="my-8 flex justify-center">
            Este ciclista no tiene etapas concluidas registradas todavía.
        </flux:heading>
    @endif

@endsection
