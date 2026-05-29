<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\User\UserProfileController;
use App\Http\Controllers\Api\User\AchievementController;

class UserProfile extends Component
{
    public $first_name = "";
    public $username = "";
    public $badges = [];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Fetch profile via API controller
        $profileRequest = Request::create('/api/user/profile', 'GET');
        $profileRequest->setUserResolver(function () {
            return Auth::user();
        });
        
        $profileController = new UserProfileController();
        $profileResponse = $profileController->index($profileRequest);
        $profileData = json_decode($profileResponse->getContent(), true)['data'];

        $this->first_name = $profileData['first_name'];
        $this->username = $profileData['username'];

    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.user_profile');
    }
}
