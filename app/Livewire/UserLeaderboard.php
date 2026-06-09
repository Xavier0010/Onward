<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\User\LeaderboardController;

class UserLeaderboard extends Component
{
    public string $type = 'xp'; // 'xp' or 'streak'
    public array $podium = [];
    public array $rankings = [];
    public ?array $currentUserRank = null;

    public function mount()
    {
        $this->loadLeaderboard();
    }

    private function apiRequest($method, $httpMethod = 'GET', $data = [], $routeParams = [])
    {
        $request = Request::create('/api/placeholder', $httpMethod, $data);
        $request->setUserResolver(fn() => Auth::user());

        $controller = new LeaderboardController();

        if (!empty($routeParams)) {
            $response = $controller->$method($request, ...$routeParams);
        } else {
            $response = $controller->$method($request);
        }

        return json_decode($response->getContent(), true);
    }

    public function loadLeaderboard()
    {
        $result = $this->apiRequest('index', 'GET', ['type' => $this->type]);

        if ($result['success'] ?? false) {
            $data = $result['data'];
            $this->podium = $data['podium'] ?? [];
            $this->rankings = $data['rankings'] ?? [];
            $this->currentUserRank = $data['current_user_rank'] ?? null;
        }
    }

    public function setType($type)
    {
        $this->type = $type;
        $this->loadLeaderboard();
    }

    public function render()
    {
        return view('livewire.user_leaderboard');
    }
}
