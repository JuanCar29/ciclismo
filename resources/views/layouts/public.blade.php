<!DOCTYPE html>
<html lang="es">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">

    {{-- Navegación --}}
    <x-public.header />

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
