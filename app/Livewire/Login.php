<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $username;
    public $password;

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required|string|min:6',
    ];

    public function render()
    {
        return view('livewire.login');
    }

    public function login() {
        try {
            $this->validate();

            $user = User::where('username', $this->username)->first();
            if (! $user || ! Hash::check($this->password, $user->password)) {
                $this->addError('login', 'Invalid username or password.');
                return;
            }
            Auth::login($user);

            return redirect()->route('home');
        } catch (\Throwable $th) {
            logger()->error($th);
        }
    }
}