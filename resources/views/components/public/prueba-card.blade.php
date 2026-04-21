@props(['prueba', 'destacada' => false])

<a href="{{ route('public.pruebas.show', $prueba) }}"
    class="block p-5 bg-white dark:bg-zinc-900 border rounded-xl transition-shadow hover:shadow-md
        {{ $destacada ? 'border-green-300 dark:border-green-800' : 'border-zinc-200 dark:border-zinc-800' }}">

    {{-- Tipo + estado --}}
    <div class="flex items-center justify-between mb-3">
        @if ($prueba->tipo === 'etapas')
            <flux:badge color="blue" variant="outline">Por etapas</flux:badge>
        @else
            <flux:badge color="amber" variant="outline">Clásica</flux:badge>
        @endif
        @if ($destacada)
            <flux:badge color="green" variant="outline">En curso</flux:badge>
        @endif
    </div>

    {{-- Nombre --}}
    <h3 class="flex items-center gap-2">
        <flux:heading size="lg" accent>{{ $prueba->nombre }}</flux:heading>
        @if ($prueba->edicion)
            <flux:text>({{ $prueba->edicion }})</flux:text>
        @endif
    </h3>

    {{-- Fechas --}}
    <flux:text class="mt-1">
        {{ $prueba->fecha_inicio->translatedFormat('d F Y') }}
        @if ($prueba->tipo === 'etapas')
            - {{ $prueba->fecha_fin->translatedFormat('d F Y') }}
        @endif
    </flux:text>

    {{-- Meta --}}
    <div class="mt-3 flex items-center gap-3 text-xs text-zinc-400">
        @if ($prueba->pais)
            <flux:text>{{ $prueba->pais }}</flux:text>
        @endif
        @if ($prueba->etapas_count > 1)
            <flux:separator vertical />
            <flux:text>{{ $prueba->etapas_count }} etapas</flux:text>
        @endif
    </div>
</a>
