@extends('layouts.public')

@section('title', 'Ciclistas — Resultados Ciclismo')

@section('content')

    <flux:card class="mb-6 flex justify-between items-center">
        <x-public.titulos> 
            <x-slot:titulo>Ciclistas</x-slot:titulo>
            <x-slot:subtitulo>Registro de ciclistas en activo.</x-slot:subtitulo>
        </x-public.titulos>
        <form method="GET" action="{{ url()->current() }}" class="flex items-end gap-3">
            <flux:select name="equipo_id" label="Filtrar por equipo" :value="request('equipo_id')"
                onchange="this.form.submit()">
                <flux:select.option value="">Todos los equipos</flux:select.option>
                @foreach ($equipos as $equipo)
                    <flux:select.option value="{{ $equipo->id }}" :selected="request('equipo_id') == $equipo->id">
                        {{ $equipo->nombre }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            @if (request('equipo_id'))
                <flux:button href="{{ url()->current() }}" variant="filled">Limpiar</flux:button>
            @endif
        </form>
    </flux:card>

    <flux:card class="p-4">
        <flux:table>
            <flux:table.columns class="bg-zinc-100 dark:bg-zinc-800">
                <flux:table.column align="center">Ciclista</flux:table.column>
                <flux:table.column align="center">Equipo</flux:table.column>
                <flux:table.column align="center">Nacionalidad</flux:table.column>
                <flux:table.column align="center">F. Nacimiento</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($ciclistas as $ciclista)
                    <flux:table.row :key="$ciclista->id">
                        <flux:table.cell class="ml-4">
                            <flux:link href="{{ route('public.ciclistas.show', $ciclista->id) }}" variant="subtle">
                                {{ $ciclista->apellidos }}, {{ $ciclista->nombre }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            <flux:badge>
                                {{ $ciclista->equipo ? $ciclista->equipo->nombre : '—' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            {{ $ciclista->nacionalidad ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            {{ $ciclista->fecha_nacimiento_formateada ?? '—' }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" align="center">
                            No hay ciclistas registrados.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="my-4 px-4">
        {{ $ciclistas->links() }}
    </div>

@endsection
