@props(['prueba', 'destacada' => false])

<a href="{{ route('public.pruebas.show', $prueba) }}"
    class="block p-5 bg-white dark:bg-zinc-900 border rounded-xl transition-shadow hover:shadow-md
        {{ $destacada ? 'border-green-300 dark:border-green-800' : 'border-zinc-200 dark:border-zinc-800' }}">

    {{-- Tipo + estado --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-medium px-2 py-0.5 rounded-full
            {{ $prueba->tipo === 'etapas' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
            {{ $prueba->tipo === 'etapas' ? 'Por etapas' : 'Clásica' }}
        </span>
        @if ($destacada)
            <span class="flex items-center gap-1 text-xs text-green-600 dark:text-green-400 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                En curso
            </span>
        @endif
    </div>

    {{-- Nombre --}}
    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 leading-snug">
        {{ $prueba->nombre }}
        @if ($prueba->edicion)
            <span class="text-zinc-400 font-normal text-sm">({{ $prueba->edicion }})</span>
        @endif
    </h3>

    {{-- Fechas --}}
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
        {{ \Carbon\Carbon::parse($prueba->fecha_inicio)->format('d M') }}
        @if ($prueba->fecha_inicio != $prueba->fecha_fin)
            — {{ \Carbon\Carbon::parse($prueba->fecha_fin)->format('d M Y') }}
        @else
            {{ \Carbon\Carbon::parse($prueba->fecha_inicio)->format('Y') }}
        @endif
    </p>

    {{-- Meta --}}
    <div class="mt-3 flex items-center gap-3 text-xs text-zinc-400">
        @if ($prueba->pais)
            <span>{{ $prueba->pais }}</span>
            <span>·</span>
        @endif
        <span>{{ $prueba->etapas_count }} etapa{{ $prueba->etapas_count !== 1 ? 's' : '' }}</span>
    </div>
</a>
