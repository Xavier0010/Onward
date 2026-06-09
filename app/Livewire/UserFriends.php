<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\User\FriendController;

class UserFriends extends Component
{
    public string $searchQuery = '';
    public string $addFriendSearch = '';

    public bool $showAddFriendModal = false;
    public bool $showPendingModal = false;
    public bool $showConfirmModal = false;

    public string $confirmAction = '';
    public string $confirmTitle = '';
    public string $confirmMessage = '';
    public mixed $confirmParam = null;

    public array $friends = [];
    public array $searchResults = [];
    public array $recommendedUsers = [];
    public array $incomingRequests = [];
    public array $outgoingRequests = [];
    public int $pendingCount = 0;

    public function mount()
    {
        $this->loadFriends();
        $this->loadPending();
    }

    private function apiRequest($method, $httpMethod = 'GET', $data = [], $routeParams = [])
    {
        $request = Request::create('/api/placeholder', $httpMethod, $data);
        $request->setUserResolver(fn() => Auth::user());

        $controller = new FriendController();

        if (!empty($routeParams)) {
            $response = $controller->$method($request, ...$routeParams);
        } else {
            $response = $controller->$method($request);
        }

        return json_decode($response->getContent(), true);
    }

    public function loadFriends()
    {
        $data = ['search' => $this->searchQuery];
        $result = $this->apiRequest('index', 'GET', $data);

        if ($result['success'] ?? false) {
            $this->friends = $result['data']->toArray ?? $result['data'];
            if (!is_array($this->friends)) {
                $this->friends = json_decode(json_encode($result['data']), true);
            }
        }
    }

    public function loadPending()
    {
        $result = $this->apiRequest('pendingRequests');
        if ($result['success'] ?? false) {
            $this->incomingRequests = $result['data']['incoming'] ?? [];
            $this->outgoingRequests = $result['data']['outgoing'] ?? [];
            $this->pendingCount = $result['data']['incoming_count'] ?? 0;

            if (!is_array($this->incomingRequests)) {
                $this->incomingRequests = json_decode(json_encode($this->incomingRequests), true);
            }
            if (!is_array($this->outgoingRequests)) {
                $this->outgoingRequests = json_decode(json_encode($this->outgoingRequests), true);
            }
        }
    }

    public function updatedSearchQuery()
    {
        $this->loadFriends();
    }

    public function updatedAddFriendSearch()
    {
        $this->searchAddFriend();
    }

    public function searchAddFriend()
    {
        if (strlen($this->addFriendSearch) < 2) {
            $this->searchResults = $this->recommendedUsers;
            return;
        }

        $result = $this->apiRequest('search', 'GET', ['q' => $this->addFriendSearch]);
        if ($result['success'] ?? false) {
            $this->searchResults = $result['data'];
            if (!is_array($this->searchResults)) {
                $this->searchResults = json_decode(json_encode($this->searchResults), true);
            }
        }
    }

    public function loadRecommendations()
    {
        $result = $this->apiRequest('search', 'GET', ['q' => '']); // Let search return recommendations if query is empty
        if ($result['success'] ?? false) {
            $this->recommendedUsers = $result['data'];
            if (!is_array($this->recommendedUsers)) {
                $this->recommendedUsers = json_decode(json_encode($this->recommendedUsers), true);
            }
            $this->searchResults = $this->recommendedUsers;
        }
    }

    public function sendRequest($userId)
    {
        $this->apiRequest('sendRequest', 'POST', ['receiver_id' => $userId]);
        $this->searchResults = [];
        $this->addFriendSearch = '';
        $this->loadPending();
        $this->closeAddFriendModal();
    }

    public function acceptRequest($friendshipId)
    {
        $this->apiRequest('acceptRequest', 'POST', [], [$friendshipId]);
        $this->loadFriends();
        $this->loadPending();
    }

    public function rejectRequest($friendshipId)
    {
        $this->apiRequest('rejectRequest', 'POST', [], [$friendshipId]);
        $this->loadPending();
    }

    public function cancelRequest($friendshipId)
    {
        $this->apiRequest('cancelRequest', 'DELETE', [], [$friendshipId]);
        $this->loadPending();
    }

    public function removeFriend($friendshipId)
    {
        $this->apiRequest('removeFriend', 'DELETE', [], [$friendshipId]);
        $this->loadFriends();
    }

    public function openAddFriendModal()
    {
        $this->showAddFriendModal = true;
        $this->addFriendSearch = '';
        $this->loadRecommendations();
    }

    public function closeAddFriendModal()
    {
        $this->showAddFriendModal = false;
    }

    public function openPendingModal()
    {
        $this->showPendingModal = true;
        $this->loadPending();
    }

    public function closePendingModal()
    {
        $this->showPendingModal = false;
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
        if ($this->confirmAction === 'removeFriend' && $this->confirmParam) {
            $this->removeFriend($this->confirmParam);
        }
        $this->showConfirmModal = false;
    }

    public function render()
    {
        return view('livewire.user_friends');
    }
}
