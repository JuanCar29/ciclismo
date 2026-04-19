@extends('layouts.public')

@section('title', $equipo->nombre . ' — Resultados Ciclismo')

@section('content')

    {{-- Breadcrumb --}}
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('public.inicio') }}">Inicio</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('public.equipos.index') }}">Equipos</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $equipo->nombre }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Perfil del Equipo --}}
    <flux:card class="mb-6 flex flex-col sm:flex-row sm:items-center gap-6">
        <flux:avatar circle size="xl" color="zinc">
            {{ $equipo->abreviatura }}
        </flux:avatar>

        <div class="flex-1">
            <flux:heading size="xl" accent>
                {{ $equipo->nombre }} ({{ $equipo->abreviatura }})
            </flux:heading>
            <div class="flex flex-wrap items-center gap-3 mt-2">
                @if ($equipo->pais)
                    <flux:text class="text-base">{{ $equipo->pais }}</flux:text>
                @endif
                @if ($equipo->web)
                    <flux:separator vertical />
                    <flux:link href="{{ $equipo->web }}" target="_blank" icon="globe-alt">{{ $equipo->web }}</flux:link>
                @endif
            </div>
        </div>

        <div class="flex gap-6 text-center shrink-0">
            <div>
                <flux:heading size="xl" accent>{{ $ciclistas->count() }}</flux:heading>
                <flux:text size="xs">Ciclistas</flux:text>
            </div>
            <div>
                <flux:heading size="xl" accent>{{ $participaciones->count() }}</flux:heading>
                <flux:text size="xs">Pruebas</flux:text>
            </div>
        </div>
    </flux:card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Plantilla Actual --}}
        <div class="lg:col-span-1">
            <flux:heading size="xl" accent class="mb-4">Plantilla Actual</flux:heading>
            <flux:card class="p-4">
                <div class="space-y-4">
                    @forelse($ciclistas as $ciclista)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <flux:avatar circle size="sm" color="indigo">
                                    {{ mb_substr(strtoupper($ciclista->nombre), 0, 1) }}{{ mb_substr(strtoupper($ciclista->apellidos), 0, 1) }}
                                </flux:avatar>
                                <div>
                                    <flux:link href="{{ route('public.ciclistas.show', $ciclista) }}" class="font-medium text-sm">
                                        {{ $ciclista->nombre }} {{ $ciclista->apellidos }}
                                    </flux:link>
                                    <flux:text size="xs">{{ $ciclista->nacionalidad }}</flux:text>
                                </div>
                            </div>
                            <flux:text size="xs">{{ $ciclista->edad }}</flux:text>
                        </div>
                        @if(!$loop->last) <flux:separator /> @endif
                    @empty
                        <flux:text>No hay ciclistas registrados en este equipo.</flux:text>
                    @endforelse
                </div>
            </flux:card>
        </div>

        {{-- Participaciones Recientes --}}
        <div class="lg:col-span-2">
            <flux:heading size="xl" accent class="mb-4">Participaciones Recientes</flux:heading>
            
            @if($participaciones->isNotEmpty())
                <flux:card class="p-4">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Prueba</flux:table.column>
                            <flux:table.column align="center">Ciclistas</flux:table.column>
                            <flux:table.column align="center">Mejor Pos.</flux:table.column>
                            <flux:table.column align="center">Aban.</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($participaciones as $p)
                                <flux:table.row :key="$p['prueba']->id">
                                    <flux:table.cell>
                                        <flux:link href="{{ route('public.pruebas.show', $p['prueba']) }}" class="font-medium">
                                            {{ $p['prueba']->nombre }}
                                        </flux:link>
                                        <div class="text-xs text-zinc-400">{{ $p['prueba']->edicion }}</div>
                                    </flux:table.cell>
                                    <flux:table.cell align="center">
                                        <span class="tabular-nums">{{ $p['ciclistas_count'] }}</span>
                                    </flux:table.cell>
                                    <flux:table.cell align="center">
                                        <flux:badge color="{{ ($p['mejor_posicion'] ?? 10) <= 3 ? 'green' : 'zinc' }}">
                                            {{ $p['mejor_posicion'] ?? '—' }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell align="center">
                                        @if($p['abandonos'] > 0)
                                            <flux:badge color="red" size="sm">{{ $p['abandonos'] }}</flux:badge>
                                        @else
                                            <span class="text-zinc-400">—</span>
                                        @endif
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            @else
                <flux:card class="p-8 text-center">
                    <flux:text>No hay participaciones registradas para este equipo.</flux:text>
                </flux:card>
            @endif

            {{-- Mejores Resultados por Etapa --}}
            <flux:heading size="xl" accent class="mb-4 mt-12">Mejores Resultados por Etapa</flux:heading>

            @if($mejoresPorEtapa->isNotEmpty())
                <flux:card class="p-4">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Etapa / Prueba</flux:table.column>
                            <flux:table.column>Mejor Ciclista</flux:table.column>
                            <flux:table.column align="center">Posición</flux:table.column>
                            <flux:table.column align="center">Tiempo</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($mejoresPorEtapa as $t)
                                <flux:table.row :key="$t->id">
                                    <flux:table.cell>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $t->etapa->nombre ?? 'Etapa ' . $t->etapa->numero }}
                                        </div>
                                        <div class="text-xs text-zinc-400">
                                            {{ $t->etapa->prueba->nombre }} ({{ $t->etapa->fecha->format('d/m/Y') }})
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:link href="{{ route('public.ciclistas.show', $t->ciclista) }}" size="sm" class="font-medium">
                                            {{ $t->ciclista->nombre }} {{ $t->ciclista->apellidos }}
                                        </flux:link>
                                    </flux:cell>
                                    <flux:table.cell align="center">
                                        @if($t->posicion <= 3)
                                            <flux:badge color="green">{{ $t->posicion }}º</flux:badge>
                                        @else
                                            <flux:badge color="zinc">{{ $t->posicion }}º</flux:badge>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell align="center">
                                        <span class="font-mono text-sm">{{ $t->tiempoFormateado() }}</span>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            @else
                <flux:card class="p-8 text-center">
                    <flux:text>No hay resultados de etapas registrados para este equipo.</flux:text>
                </flux:card>
            @endif
        </div>
    </div>

@endsection
