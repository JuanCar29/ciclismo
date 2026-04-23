<header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-24">

            {{-- Logo --}}
            <a href="{{ route('public.inicio') }}" class="flex items-center gap-8">
                <x-app-logo-icon class="size-12 text-black dark:text-white" />
                <flux:heading size="2xl" accent>
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
                <flux:navbar.item href="{{ route('public.equipos.index') }}"
                    :current="request()->routeIs('public.equipos.*')">
                    Equipos
                </flux:navbar.item>
                <flux:navbar.item href="{{ route('login') }}" :current="request()->routeIs('login')">
                    Admin
                </flux:navbar.item>
            </flux:navbar>
        </div>
    </div>
</header>