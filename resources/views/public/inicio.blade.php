@extends('layouts.public', ['title' => 'Resultados Ciclismo'])

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
            <flux:heading size="xl" level="2" accent>Próximas pruebas</flux:heading>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($proximas as $prueba)
                    <x-public.prueba-card :prueba="$prueba" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Pasadas --}}
    @if ($pasadas->isNotEmpty())
        <section>
            <div class="flex items-center justify-between">
                <flux:heading size="xl" level="2" accent>Pruebas recientes</flux:heading>
                <flux:link href="{{ route('public.pruebas.index') }}" variant="ghost">Ver todas →</flux:link>
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pasadas as $prueba)
                    <x-public.prueba-card :prueba="$prueba" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Estado vacío --}}
    @if ($enCurso->isEmpty() && $proximas->isEmpty() && $pasadas->isEmpty())
        <div class="text-center py-24">
            <flux:heading size="lg" level="4">No hay pruebas registradas todavía.</flux:heading>
        </div>
    @endif

@endsection
