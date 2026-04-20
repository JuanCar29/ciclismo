@extends('layouts.public')

@section('title', 'Página no encontrada — 404')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[70vh] text-center px-4 relative overflow-hidden">
        
        {{-- Fondo decorativo sutil --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-500/5 rounded-full blur-3xl -z-10"></div>

        {{-- Icono/Ilustración --}}
        <div class="relative mb-12 group">
            {{-- Efecto de brillo exterior --}}
            <div class="absolute -inset-4 bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 rounded-full blur-2xl opacity-100 group-hover:scale-110 transition-all duration-700"></div>
            
            <div class="relative bg-white dark:bg-zinc-900 p-10 rounded-full shadow-2xl border border-zinc-100 dark:border-zinc-800/50">
                <div class="relative">
                    {{-- Icono principal: Un reloj (tiempo) que es común en ciclismo --}}
                    <flux:icon.clock class="size-32 text-indigo-600 dark:text-indigo-400" />
                    
                    {{-- Pequeño icono de advertencia superpuesto --}}
                    <div class="absolute -top-2 -right-2 bg-red-500 text-white p-2 rounded-full shadow-lg">
                        <flux:icon.exclamation-triangle class="size-6" />
                    </div>
                </div>
            </div>
            
            {{-- Badge de Error --}}
            <div class="absolute -bottom-6 left-1/2 -translate-x-1/2">
                <div class="bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 px-6 py-2 rounded-full text-sm font-black tracking-[0.2em] shadow-xl">
                    404 ERROR
                </div>
            </div>
        </div>

        {{-- Textos --}}
        <div class="max-w-2xl mx-auto">
            <flux:heading size="2xl" accent class="mb-4">
                ¡Vaya! Te has salido de la ruta
            </flux:heading>
            
            <flux:text size="xl" class="mb-10 leading-relaxed balance">
                Parece que la etapa que buscas no está disponible o has tomado un desvío hacia una ruta inexistente.
                El pelotón principal te espera en la meta.
            </flux:text>
        </div>

        {{-- Acciones --}}
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center w-full max-w-md">
            <flux:button href="{{ route('public.inicio') }}" variant="primary" icon="home">
                Volver al inicio
            </flux:button>
            
            <flux:button onclick="window.history.back()" variant="filled" icon="arrow-left" class="cursor-pointer">
                Página anterior
            </flux:button>
        </div>

        {{-- Sugerencias rápidas --}}
        <div class="mt-16 pt-8 border-t border-zinc-100 dark:border-zinc-800/50 w-full max-w-sm">
            <flux:text size="sm" class="mb-4 text-zinc-400 uppercase tracking-widest font-semibold">¿Buscas algo específico?</flux:text>
            <div class="flex gap-4 justify-center">
                <flux:link href="{{ route('public.pruebas.index') }}" class="text-sm font-medium">Pruebas</flux:link>
                <flux:separator vertical variant="subtle" />
                <flux:link href="{{ route('public.ciclistas.index') }}" class="text-sm font-medium">Ciclistas</flux:link>
                <flux:separator vertical variant="subtle" />
                <flux:link href="{{ route('public.equipos.index') }}" class="text-sm font-medium">Equipos</flux:link>
            </div>
        </div>
    </div>

    {{-- Estilos ad-hoc para la tipografía "balance" si no está disponible por defecto --}}
    <style>
        .balance {
            text-wrap: balance;
        }
    </style>
@endsection
