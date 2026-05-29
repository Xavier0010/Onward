<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;

class Register extends Component
{
    public $username = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $first_name = '';
    public $last_name = '';
    public $sex = '';
    public $date_of_birth = '';

    protected $rules = [
        'username' => 'required|min:6|unique:users,username',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'first_name' => 'required',
        'last_name' => 'required',
        'sex' => 'required|in:male,female',
        'date_of_birth' => 'required|date',
    ];

    public function signup()
    {
        $validated = $this->validate();

        $request = Request::create('/api/register', 'POST', $validated);
        
        $controller = new AuthController();
        $response = $controller->register($request);

        if ($response->status() === 201) {
            $user = User::where('email', $this->email)->first();
            Auth::login($user);
            return redirect('/profile');
        } else {
            $data = json_decode($response->getContent(), true);
            $this->addError('email', $data['message'] ?? 'Registration failed.');
        }
    }

    public function render()
    {
        return view('livewire.register');
    }
}
