@extends('layouts.public', ['title' => 'Pruebas'])

@section('content')

    <flux:card class="mb-6">
        <x-public.titulos> 
            <x-slot:titulo>Pruebas</x-slot:titulo>
            <x-slot:subtitulo>Todas las carreras y pruebas ciclistas.</x-slot:subtitulo>
        </x-public.titulos>
    </flux:card>

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
