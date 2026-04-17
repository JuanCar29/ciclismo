<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Resultados de pruebas ciclistas">
    <title>@yield('title', 'Resultados Ciclismo')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">

    {{-- Navegación --}}
    <nav class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('public.inicio') }}" class="flex items-center gap-2">
                    <flux:icon.clock />
                    <flux:heading size="xl" accent>
                        Resultados Ciclismo
                    </flux:heading>
                </a>

                {{-- Menú --}}
                <flux:navbar>
                    <flux:navbar.item href="{{ route('public.pruebas.index') }}"
                        :current="request()->routeIs('public.pruebas.*')">
                        Pruebas
                    </flux:navbar.item>
                    <flux:navbar.item href="{{ route('public.ciclistas.index') }}"
                        :current="request()->routeIs('public.ciclistas.*')">
                        Ciclistas
                    </flux:navbar.item>
                    <flux:navbar.item href="{{ route('login') }}" :current="request()->routeIs('login')">
                        Admin
                    </flux:navbar.item>
                </flux:navbar>
            </div>
        </div>
    </nav>

    {{-- Contenido --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-16 border-t border-zinc-200 dark:border-zinc-800 py-8 flex items-center justify-center">
        <flux:text size="base">
            Resultados Ciclismo · {{ date('Y') }}
        </flux:text>
    </footer>

</body>

</html>
