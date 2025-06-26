<?php

namespace App\Livewire\Header;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserProfileDropdown extends Component
{
    public string $name;
    public string $email;

    /**
     * Saat komponen pertama kali dimuat, inisialisasi properti
     * dengan data pengguna yang sedang login.
     */
    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    /**
     * Method ini akan "mendengarkan" event 'profile-updated' yang dikirim
     * dari komponen lain dan memperbarui nama.
     *
     * Untuk Livewire 2, gunakan protected $listeners = ['profile-updated' => 'updateName'];
     */
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
