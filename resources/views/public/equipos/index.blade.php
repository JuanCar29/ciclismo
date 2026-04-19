@extends('layouts.public')

@section('title', 'Equipos — Resultados Ciclismo')

@section('content')

    <flux:card class="mb-6">
        <x-public.titulos>
            <x-slot:titulo>Equipos</x-slot:titulo>
            <x-slot:subtitulo>Listado de escuadras registradas.</x-slot:subtitulo>
        </x-public.titulos>
    </flux:card>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($equipos as $equipo)
            <flux:card class="hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <flux:avatar circle size="lg" color="zinc">
                        {{ $equipo->abreviatura }}
                    </flux:avatar>
                    <div>
                        <flux:link href="{{ route('public.equipos.show', $equipo) }}" class="text-lg font-bold">
                            {{ $equipo->nombre }}
                        </flux:link>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">País:</span>
                        <span class="font-medium">{{ $equipo->pais ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Ciclistas en plantilla:</span>
                        <span class="font-medium">{{ $equipo->ciclistas_count }}</span>
                    </div>
                </div>

                <flux:separator class="my-4" />

                <div class="flex justify-between items-center">
                    @if($equipo->web)
                        <flux:link href="{{ $equipo->web }}" target="_blank" variant="subtle" size="sm" icon="globe-alt">
                            Sitio Web
                        </flux:link>
                    @else
                        <span></span>
                    @endif
                    <flux:button href="{{ route('public.equipos.show', $equipo) }}" variant="ghost" size="sm">
                        Ver perfil
                    </flux:button>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full">
                <flux:card class="p-8 text-center">
                    <flux:text size="lg">No hay equipos registrados.</flux:text>
                </flux:card>
            </div>
        @endforelse
    </div>

    <div class="my-8 px-4">
        {{ $equipos->links() }}
    </div>

@endsection
