<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilePage extends Component
{
    use WithFileUploads;

    public $name, $email, $phone_number, $nik;
    public $current_password, $new_password, $new_password_confirmation;
    public $avatar;
    public $showPasswordForm = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number,' . Auth::id(),
            'nik' => 'nullable|string|max:20|unique:users,nik,' . Auth::id(),
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    protected function passwordRules()
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->nik = $user->nik;
    }

    public function updateProfile()
    {
        $this->validate();

        $user = Auth::user();
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'nik' => $this->nik,
        ];

        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar_url) {
                Storage::delete('storage/avatars' . $user->avatar_url);
            }

            // Store new avatar
            $path = $this->avatar->store('avatars', 'public');
            $data['avatar_url'] = $path;
        }

        $user->update($data);

        session()->flash('message', 'Profil berhasil diperbarui.');
        
        // Reset avatar input
        $this->avatar = null;
    }

    public function updatePassword()
    {
        $this->validate($this->passwordRules());

        $user = Auth::user();

        // Check current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        // Reset password fields
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->showPasswordForm = false;

        session()->flash('message', 'Password berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.profile.profile-page');
    }
}