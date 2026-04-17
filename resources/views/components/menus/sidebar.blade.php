<flux:sidebar.nav>
    <flux:sidebar.group :heading="__('Platform')" class="grid">
        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
            wire:navigate>
            {{ __('Dashboard') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="user-group" :href="route('equipos')" :current="request()->routeIs('equipos')"
            wire:navigate>
            {{ __('Teams') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="user-circle" :href="route('ciclistas')" :current="request()->routeIs('ciclistas')"
            wire:navigate>
            {{ __('Cyclists') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="list-bullet" :href="route('pruebas')" :current="request()->routeIs('pruebas')"
            wire:navigate>
            {{ __('Tests') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="users" :href="route('usuarios')" :current="request()->routeIs('usuarios')"
            wire:navigate>
            {{ __('Users') }}
        </flux:sidebar.item>
    </flux:sidebar.group>
</flux:sidebar.nav>
