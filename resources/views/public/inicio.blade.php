@extends('layouts.public')

@section('title', 'Inicio — Resultados Ciclismo')

@section('content')

    {{-- Hero --}}
    <flux:card class="mb-6">
        <x-public.titulos titulo="Resultados Ciclismo" subtitulo="Clasificaciones y resultados de pruebas ciclistas." />
    </flux:card>

    {{-- En curso --}}
    @if ($enCurso->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <flux:heading size="lg">En curso</flux:heading>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($enCurso as $prueba)
                    <x-public.prueba-card :prueba="$prueba" destacada />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Próximas --}}
    @if ($proximas->isNotEmpty())
        <section class="mb-10">
            <flux:heading size="lg" class="mb-4">Próximas pruebas</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($proximas as $prueba)
                    <x-public.prueba-card :prueba="$prueba" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Pasadas --}}
    @if ($pasadas->isNotEmpty())
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Pruebas recientes</h2>
                <a href="{{ route('public.pruebas.index') }}"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Ver todas →
                </a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pasadas as $prueba)
                    <x-public.prueba-card :prueba="$prueba" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Estado vacío --}}
    @if ($enCurso->isEmpty() && $proximas->isEmpty() && $pasadas->isEmpty())
        <div class="text-center py-24 text-zinc-400">
            <p class="text-lg">No hay pruebas registradas todavía.</p>
        </div>
    @endif

@endsection
