<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AvatarBorder;
use App\Models\UserAvatarBorder;
use App\Models\Todo;

class Store extends Component
{
    public $user;
    public $borders;
    public $ownedBorderIds;
    public $activeBorderId;
    public $totalXp;
    public $availableXp;
    
    public $showNotificationModal = false;
    public $notificationType = 'success';
    public $notificationTitle = '';
    public $notificationMessage = '';

    public function mount()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        $this->user = Auth::user();
        
        // Get all avatar borders and group them carefully
        $this->borders = AvatarBorder::all()->groupBy('rarity')->toArray();
        
        // Get owned border IDs safely
        $this->ownedBorderIds = UserAvatarBorder::where('user_id', $this->user->id)
            ->pluck('avatar_border_id')
            ->toArray();
        
        // Get active border ID safely
        $activeRecord = UserAvatarBorder::where('user_id', $this->user->id)
            ->where('active', true)
            ->first();
        $this->activeBorderId = $activeRecord ? $activeRecord->avatar_border_id : null;
        
        // Calculate XP exactly like dashboard
        $this->calculateXp();
    }
    
    public function calculateXp()
    {
        // Calculate task XP
        $taskXp = Todo::where('user_id', $this->user->id)
            ->where('status', 3)
            ->get()
            ->sum(function ($todo) {
                switch ((int) $todo->priority) {
                    case 1: return 10;
                    case 2: return 15;
                    case 3: return 20;
                    default: return 10;
                }
            });
        
        // Calculate streak XP
        $streakXp = 0;
        if ($this->user->streak_count >= 30) $streakXp += 200;
        if ($this->user->streak_count >= 7) $streakXp += 50;
        
        $this->totalXp = $taskXp + $streakXp;
        $this->availableXp = $this->totalXp - ($this->user->spent_xp ?? 0);
    }

    public function buyBorder($borderId)
    {
        $border = AvatarBorder::findOrFail($borderId);
        
        // Check if already owned
        if (in_array($borderId, $this->ownedBorderIds)) {
            $this->showNotificationModal = true;
            $this->notificationType = 'warning';
            $this->notificationTitle = 'Already Owned';
            $this->notificationMessage = 'You already own this border!';
            return;
        }
        
        // Re-calculate XP to make sure we have up-to-date values
        $this->calculateXp();
        
        // Check XP
        if ($this->availableXp < $border->price) {
            $this->showNotificationModal = true;
            $this->notificationType = 'error';
            $this->notificationTitle = 'Not Enough XP';
            $this->notificationMessage = 'You need ' . $border->price . ' XP to buy this border!';
            return;
        }
        
        // Deduct XP
        $this->user->increment('spent_xp', $border->price);
        
        // Create ownership record
        UserAvatarBorder::create([
            'user_id' => $this->user->id,
            'avatar_border_id' => $borderId,
            'active' => false
        ]);
        
        // Refresh state
        $this->user->refresh();
        $this->ownedBorderIds = UserAvatarBorder::where('user_id', $this->user->id)
            ->pluck('avatar_border_id')->toArray();
        $this->calculateXp();
        
        // Show success notification
        $this->showNotificationModal = true;
        $this->notificationType = 'success';
        $this->notificationTitle = 'Purchase Successful!';
        $this->notificationMessage = 'You bought the ' . $border->name . '!';
    }
    
    public function setActiveBorder($borderId)
    {
        // Check if owned
        if (!in_array($borderId, $this->ownedBorderIds)) {
            $this->showNotificationModal = true;
            $this->notificationType = 'error';
            $this->notificationTitle = 'Not Owned';
            $this->notificationMessage = 'You don\'t own this border yet!';
            return;
        }
        
        // Deactivate all
        UserAvatarBorder::where('user_id', $this->user->id)->update(['active' => false]);
        
        // Activate selected
        UserAvatarBorder::where('user_id', $this->user->id)
            ->where('avatar_border_id', $borderId)
            ->update(['active' => true]);
            
        $this->activeBorderId = $borderId;
        
        // Get border info for notification
        $border = AvatarBorder::find($borderId);
        
        $this->showNotificationModal = true;
        $this->notificationType = 'success';
        $this->notificationTitle = 'Border Equipped!';
        $this->notificationMessage = 'You are now using the ' . $border->name . '!';
    }

    public function render()
    {
        return view('livewire.store');
    }
}
