@extends('layouts.public')

@section('title', 'Pruebas — Resultados Ciclismo')

@section('content')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">Pruebas</h1>
        <p class="mt-2 text-zinc-500 dark:text-zinc-400">Todas las carreras y pruebas ciclistas.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($pruebas as $prueba)
            @php
                $enCurso  = now()->between($prueba->fecha_inicio, $prueba->fecha_fin);
            @endphp
            <x-public.prueba-card :prueba="$prueba" :destacada="$enCurso" />
        @empty
            <div class="col-span-3 text-center py-24 text-zinc-400">
                No hay pruebas registradas todavía.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $pruebas->links() }}
    </div>

@endsection
