<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';


    public $confirmingUserId = null;
    public $confirmingUserName = '';

    public function confirmToggleRole($userId)
    {
        $user = User::findOrFail($userId);

        // Sécurité : empêcher l'auto-modification
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas modifier votre propre rôle.'
            ]);
            return;
        }

        $this->confirmingUserId = $user->id;
        $this->confirmingUserName = $user->name;
    }

    public function toggleRole()
    {
        if (!$this->confirmingUserId) {
            return;
        }
        // $user = User::findOrFail($userId);
        $user = User::findOrFail($this->confirmingUserId);

        // Empêcher un admin de se retirer lui-même
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas modifier votre propre rôle.'
            ]);
            return;
        }

        $user->user_role = $user->user_role === 'admin' ? 'user' : 'admin';
        $user->save();
        $this->reset(['confirmingUserId', 'confirmingUserName']);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Rôle utilisateur mis à jour avec succès.'
        ]);
    }

    public function render()
    {
        // dd(User::count());
        return view('livewire.admin.users', [
            'users' => User::orderBy('name')->paginate(10),
            'countUsers' => User::count()
        ]);
    }
}
