@extends('layouts.public')

@section('title', $prueba->nombre . ' — Resultados Ciclismo')

@section('content')

    {{-- Breadcrumb --}}
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('public.inicio')">Inicio</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('public.pruebas.index')">Pruebas</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $prueba->nombre }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Cabecera --}}
    <flux:card class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <flux:badge color="indigo">{{ $prueba->tipo === 'etapas' ? 'Por etapas' : 'Clásica' }}</flux:badge>
                @if ($prueba->pais)
                    <flux:text>{{ $prueba->pais }}</flux:text>
                @endif
            </div>
            <x-public.titulos> 
                <x-slot:titulo>{{ $prueba->nombre }} {{ $prueba->edicion }}</x-slot:titulo>
                <x-slot:subtitulo>
                    <div class="flex items-center gap-2">
                        @if ($etapas->count() > 1)
                            {{ $prueba->fecha_inicio->translatedFormat('d F Y') }} - {{ $prueba->fecha_fin->translatedFormat('d F Y') }}
                            <flux:separator variant="subtle" vertical />
                            {{ $etapas->count() }} etapas
                        @else
                            {{ $etapas->first()->fecha->translatedFormat('d F Y') }}
                        @endif
                    </div>
                </x-slot:subtitulo>
            </x-public.titulos>
        </div>

        {{-- Stats --}}
        <div class="flex items-end gap-6 text-center shrink-0">
            @if ($etapas->count() > 1)
                <div>
                    <flux:heading size="xl" accent>{{ $etapas->count() }}</flux:heading>
                    <flux:text size="xs">Etapas</flux:text>
                </div>
            @endif
            <div>
                <flux:heading size="xl" accent>{{ $totalParticipantes }}</flux:heading>
                <flux:text size="xs">Ciclistas</flux:text>
            </div>
            <div>
                <flux:heading size="xl" accent>{{ $abandonos }}</flux:heading>
                <flux:text size="xs">Abandonos</flux:text>
            </div>
        </div>
    </flux:card>

    {{-- Accesos a clasificaciones --}}
    <div class="grid sm:grid-cols-4 gap-3 mb-10">

        <a href="{{ route('public.clasificacion.general', $prueba) }}" aria-label="Latest on our blog">
            <flux:card size="sm"
                class="hover:bg-zinc-50 hover:border-indigo-400 flex items-center gap-4 transition-colors cursor-pointer">
                <flux:icon.chart-bar variant="solid" class="text-indigo-600" />
                <div>
                    <flux:heading>Clasificación general</flux:heading>
                    <flux:text>Tiempos acumulados</flux:text>
                </div>
            </flux:card>
        </a>

        <a href="{{ route('public.clasificacion.puntos', $prueba) }}" aria-label="Latest on our blog">
            <flux:card size="sm"
                class="hover:bg-zinc-50 hover:border-indigo-400 flex items-center gap-4 transition-colors cursor-pointer">
                <flux:icon.chart-bar variant="solid" class="text-indigo-600" />
                <div>
                    <flux:heading>Clasificación por puntos</flux:heading>
                    <flux:text>Puntos acumulados</flux:text>
                </div>
            </flux:card>
        </a>

        <a href="{{ route('public.clasificacion.equipos', $prueba) }}" aria-label="Latest on our blog">
            <flux:card size="sm"
                class="hover:bg-zinc-50 hover:border-amber-400 flex items-center gap-4 transition-colors cursor-pointer">
                <flux:icon.user-group variant="solid" class="text-amber-600" />
                <div>
                    <flux:heading>Clasificación equipos</flux:heading>
                    <flux:text>3 mejores por equipo</flux:text>
                </div>
            </flux:card>
        </a>

        <flux:card size="sm" class="hover:bg-zinc-50 hover:border-zinc-400 flex items-center gap-4 transition-colors">
            <flux:icon.list-bullet variant="solid" class="text-zinc-600" />
            <div>
                <flux:heading>Por etapas</flux:heading>
                <flux:text>Selecciona una etapa abajo</flux:text>
            </div>
        </flux:card>

    </div>

    {{-- Etapas --}}
    <flux:heading size="lg" class="mb-4" accent>Etapas</flux:heading>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
        @forelse ($etapas as $etapa)
            <a href="{{ route('public.clasificacion.etapa', [$prueba, $etapa]) }}"
                class="flex items-center gap-4 px-5 py-4 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors
                    {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }}">

                {{-- Número --}}
                <flux:badge>{{ $etapa->numero }}</flux:badge>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <flux:heading size="sm" class="truncate">
                        {{ $etapa->nombre ?? ($etapa->salida && $etapa->llegada ? $etapa->salida . ' — ' . $etapa->llegada : 'Etapa ' . $etapa->numero) }}
                    </flux:heading>
                    <div class="flex gap-2 mt-0.5">
                        <flux:text>{{ $etapa->fecha->translatedFormat('d F Y') }}</flux:text>
                        @if ($etapa->distancia_km)
                            <flux:separator vertical />
                            <flux:text>{{ number_format($etapa->distancia_km, 1, ',', '.') }} km</flux:text>
                        @endif
                    </div>
                </div>

                {{-- Tipo --}}
                @php
                    $colores = [
                        'llano' => 'green',
                        'media_montana' => 'yellow',
                        'alta_montana' => 'red',
                        'contrarreloj' => 'indigo',
                        'contrarreloj_por_equipos' => 'purple',
                    ];
                    $etiquetas = [
                        'llano' => 'Llano',
                        'media_montana' => 'Media montaña',
                        'alta_montana' => 'Alta montaña',
                        'contrarreloj' => 'CRI',
                        'contrarreloj_por_equipos' => 'CRE',
                    ];
                @endphp
                <flux:badge color="{{ $colores[$etapa->tipo] }}" size="sm">{{ $etiquetas[$etapa->tipo] }}
                </flux:badge>

                {{-- Tiempos registrados --}}
                <flux:text>
                    @if ($etapa->tiempos_count > 0)
                        {{ $etapa->tiempos_count }} tiempos
                    @else
                        Sin datos
                    @endif
                </flux:text>

                {{-- Flecha --}}
                <flux:icon.chevron-right variant="solid" class="text-zinc-400" />
            </a>
        @empty
            <flux:heading size="lg">No hay etapas registradas para esta prueba.</flux:heading>
        @endforelse
    </div>

@endsection
