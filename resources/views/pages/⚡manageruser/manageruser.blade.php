<div class="max-w-7xl mx-auto p-4 border border-zinc-200 rounded-lg shadow-sm">
    <flux:toast position="top-end" />
    {{-- Cabecera de página --}}
    <flux:heading size="xl">Usuarios</flux:heading>
    <flux:subheading>Gestión del registro de usuarios</flux:subheading>

    <div class="flex flex-col bg-zinc-50 gap-4 mt-6 p-4 rounded-lg">
        <flux:heading size="xl">{{ $editingUserId ? 'Editar Usuario' : 'Crear Usuario' }}</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input wire:model="name" label="Nombre" />
            <flux:input wire:model="email" label="Correo Electrónico" />
            <flux:input wire:model="password" type="password" label="Contraseña" viewable />
            <flux:field>
                <flux:label>Administrador</flux:label>
                <flux:switch wire:model="is_admin" />
            </flux:field>
        </div>
        <div class="flex items-center gap-4">
            <flux:button wire:click="{{ $editingUserId ? 'updateUser' : 'createUser' }}" variant="primary"
                icon="check-circle" size="sm" class="cursor-pointer">
                {{ $editingUserId ? 'Actualizar Usuario' : 'Crear Usuario' }}
            </flux:button>
            @if ($editingUserId)
                <flux:button wire:click="resetForm" icon="x-circle" size="sm" class="cursor-pointer">
                    Cancelar
                </flux:button>
            @endif
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm mt-6">
        <flux:heading size="xl">Tabla de usuarios</flux:heading>
        <flux:table :paginate="$this->users">
            <flux:table.columns>
                <flux:table.column align="center">Nombre</flux:table.column>
                <flux:table.column align="center">Correo Electrónico</flux:table.column>
                <flux:table.column align="center">Administrador</flux:table.column>
                <flux:table.column align="center">Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->users as $user)
                    <flux:table.row :key="$user->id">
                        <flux:table.cell>
                            {{ $user->name }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $user->email }}
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            {{ $user->is_admin ? 'Sí' : 'No' }}
                        </flux:table.cell>
                        <flux:table.cell class="flex justify-center gap-4">
                            <flux:button wire:click="editUser({{ $user->id }})" variant="filled" icon="pencil"
                                size="xs" class="cursor-pointer">
                                Editar
                            </flux:button>
                            <flux:button wire:click="deleteUser({{ $user->id }})" variant="danger" icon="trash"
                                size="xs" class="cursor-pointer">
                                Eliminar
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center">
                            No hay usuarios registrados.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
