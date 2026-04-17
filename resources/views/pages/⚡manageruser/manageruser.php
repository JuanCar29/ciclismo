<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Gestión de Usuarios')] class extends Component
{
    use WithPagination;

    public $name;

    public $email;

    public $password;

    public $is_admin;

    public $editingUserId = null;

    #[Computed]
    public function Users()
    {
        return User::orderBy('name')
            ->paginate(10);
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_admin' => $this->is_admin,
        ]);

        $this->resetForm();
        Flux::Toast(
            heading: 'Usuario creado',
            text: "El usuario {$this->name} ha sido creado exitosamente.",
            variant: 'success',
        );
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = $user->is_admin;
    }

    public function updateUser()
    {
        if (! $this->editingUserId) {
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$this->editingUserId}",
            'password' => 'nullable|string|min:8',
            'is_admin' => 'boolean',
        ]);

        $user = User::findOrFail($this->editingUserId);
        $user->name = $this->name;
        $user->email = $this->email;
        if ($this->password) {
            $user->password = Hash::make($this->password);
        }
        $user->is_admin = $this->is_admin;
        $user->save();

        $this->resetForm();
        Flux::Toast(
            heading: 'Usuario actualizado',
            text: "El usuario {$user->name} ha sido actualizado exitosamente.",
            variant: 'success',
        );
    }

    public function deleteUser($id)
    {
        User::destroy($id);
        if ($this->editingUserId == $id) {
            $this->resetForm();
        }
        Flux::Toast(
            heading: 'Usuario eliminado',
            text: 'El usuario ha sido eliminado exitosamente.',
            variant: 'danger',
        );
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->is_admin = false;
        $this->editingUserId = null;
    }
};
