<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;

class Login extends Component
{
    public $login_field;
    public $password;

    protected $rules = [
        'login_field' => 'required',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        $request = Request::create('/api/login', 'POST', [
            'login' => $this->login_field,
            'password' => $this->password
        ]);
        
        $controller = new AuthController();
        $response = $controller->login($request);

        if ($response->status() === 200) {
            $loginType = filter_var($this->login_field, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $user = User::where($loginType, $this->login_field)->first();
            
            Auth::login($user);
            session()->regenerate();
            
            return redirect('/user/profile');
        }

        $data = json_decode($response->getContent(), true);
        $this->addError('login_field', $data['message'] ?? 'Credentials incorrect or not registered!');
    }

    public function render()
    {
        return view('livewire.login');
    }
}
