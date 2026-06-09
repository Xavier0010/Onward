<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\User\UserProfileController;
use App\Http\Controllers\Api\User\AchievementController;
use App\Http\Controllers\Api\User\FriendController;

class UserProfile extends Component
{
    use WithFileUploads;

    public array $countries = [
        "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria",
        "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan",
        "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia",
        "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo", "Costa Rica",
        "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador",
        "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France",
        "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau",
        "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland",
        "Israel", "Italy", "Ivory Coast", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo",
        "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania",
        "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius",
        "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia",
        "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway",
        "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland",
        "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino",
        "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands",
        "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland",
        "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey",
        "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu",
        "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
    ];

    public ?string $profileUserId = null;
    public bool $isOwnProfile = false;
    public array $profileData = [];

    // Modals
    public bool $showEditProfileModal = false;
    public bool $showAvatarModal = false;
    public bool $showAchievementsModal = false;

    // Edit fields
    public string $editFirstName = '';
    public string $editLastName = '';
    public string $editGender = '';
    public string $editNationality = '';
    public string $editOldPassword = '';
    public string $editNewPassword = '';
    public string $editNewPasswordConfirmation = '';

    // Avatar
    public $avatarUpload = null;

    // Confirmation modal
    public bool $showConfirmModal = false;
    public string $confirmAction = '';
    public string $confirmTitle = '';
    public string $confirmMessage = '';
    public mixed $confirmParam = null;

    // Achievement showcase
    public array $allAchievements = [];
    public array $selectedAchievementIds = [];

    private function apiRequest($controllerClass, $method, $httpMethod = 'GET', $data = [], $routeParams = [])
    {
        $request = Request::create('/api/placeholder', $httpMethod, $data);
        $request->setUserResolver(fn() => Auth::user());

        if (!empty($data['_files'])) {
            foreach ($data['_files'] as $key => $file) {
                $request->files->set($key, $file);
            }
        }

        $controller = new $controllerClass();

        if (!empty($routeParams)) {
            $response = $controller->$method($request, ...$routeParams);
        } else {
            $response = $controller->$method($request);
        }

        return json_decode($response->getContent(), true);
    }

    public function mount($userId = null)
    {
        $this->profileUserId = $userId;
        $this->loadProfile();
    }

    public function loadProfile()
    {
        if ($this->profileUserId) {
            $result = $this->apiRequest(UserProfileController::class, 'show', 'GET', [], [$this->profileUserId]);
        } else {
            $result = $this->apiRequest(UserProfileController::class, 'index');
        }

        if ($result['success'] ?? false) {
            $this->profileData = $result['data'];
            $this->isOwnProfile = $this->profileData['is_own_profile'] ?? false;
        }
    }

    // ── Edit Profile Modal ──

    public function openEditProfileModal()
    {
        $this->editFirstName = $this->profileData['first_name'] ?? '';
        $this->editLastName = $this->profileData['last_name'] ?? '';
        $this->editGender = $this->profileData['gender'] ?? '';
        $this->editNationality = $this->profileData['nationality'] ?? '';
        $this->editOldPassword = '';
        $this->editNewPassword = '';
        $this->editNewPasswordConfirmation = '';
        $this->showEditProfileModal = true;
    }

    public function saveProfile()
    {
        $this->validate([
            'editFirstName' => 'required|string',
            'editLastName' => 'required|string',
            'editGender' => 'required|in:male,female',
            'editNationality' => 'nullable|string|max:100',
            'editOldPassword' => 'required_with:editNewPassword',
            'editNewPassword' => 'nullable|min:8|confirmed',
        ]);

        $this->apiRequest(UserProfileController::class, 'update', 'PUT', [
            'first_name' => $this->editFirstName,
            'last_name' => $this->editLastName,
            'gender' => $this->editGender,
            'nationality' => $this->editNationality,
            'old_password' => $this->editOldPassword,
            'new_password' => $this->editNewPassword,
        ]);

        $this->showEditProfileModal = false;
        $this->loadProfile();
    }

    public function openConfirmModal($action, $title, $message, $param = null)
    {
        $this->confirmAction = $action;
        $this->confirmTitle = $title;
        $this->confirmMessage = $message;
        $this->confirmParam = $param;
        $this->showConfirmModal = true;
    }

    public function executeConfirmAction()
    {
        if ($this->confirmAction === 'removeFriend') {
            $this->removeFriend();
        }
        $this->showConfirmModal = false;
    }

    // ── Avatar Modal ──

    public function openAvatarModal()
    {
        $this->avatarUpload = null;
        $this->showAvatarModal = true;
    }

    public function saveAvatar()
    {
        $this->validate([
            'avatarUpload' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();
        if ($user->avatar && \Illuminate\Support\Facades\Storage::exists($user->avatar)) {
            \Illuminate\Support\Facades\Storage::delete($user->avatar);
        }
        $path = $this->avatarUpload->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        $this->showAvatarModal = false;
        $this->avatarUpload = null;
        $this->loadProfile();
    }

    // ── Achievements Modal ──

    public function openAchievementsModal()
    {
        $result = $this->apiRequest(AchievementController::class, 'index');
        if ($result['success'] ?? false) {
            $this->allAchievements = collect($result['data'])->filter(fn($a) => $a['unlocked'])->values()->toArray();
        }

        $this->selectedAchievementIds = collect($this->profileData['showcase_achievements'] ?? [])
            ->pluck('id')->toArray();

        $this->showAchievementsModal = true;
    }

    public function toggleAchievement($id)
    {
        if (in_array($id, $this->selectedAchievementIds)) {
            $this->selectedAchievementIds = array_values(array_diff($this->selectedAchievementIds, [$id]));
        } else {
            if (count($this->selectedAchievementIds) < 8) {
                $this->selectedAchievementIds[] = $id;
            }
        }
    }

    public function saveShowcaseAchievements()
    {
        $this->apiRequest(UserProfileController::class, 'updateShowcaseAchievements', 'PUT', [
            'achievement_ids' => $this->selectedAchievementIds,
        ]);

        $this->showAchievementsModal = false;
        $this->loadProfile();
    }

    // ── Friend Actions (for other's profile) ──

    public function sendFriendRequest()
    {
        $this->apiRequest(FriendController::class, 'sendRequest', 'POST', [
            'receiver_id' => $this->profileData['id'],
        ]);
        $this->loadProfile();
    }

    public function removeFriend()
    {
        if ($this->profileData['friendship_id']) {
            $this->apiRequest(FriendController::class, 'removeFriend', 'DELETE', [], [$this->profileData['friendship_id']]);
            $this->loadProfile();
        }
    }

    public function render()
    {
        return view('livewire.user_profile');
    }
}
