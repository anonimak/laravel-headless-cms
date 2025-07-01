<?php

namespace App\Livewire\Common;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserProfileDropdown extends Component
{
    public string $name;
    public string $email;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    // [On('profile-updated')]
    public function updateName(string $newName)
    {
        $this->name = $newName;
    }

    public function logout()
    {
        Auth::logout();
        return $this->redirect('/', navigate: true); // Gunakan Livewire 3 navigation
    }
}
