# 🤝 Onward Friends + Profile System — Implementation Plan v2

> **Key Rule**: ALL Livewire components consume REST API endpoints. No direct Eloquent in Livewire.  
> **Pattern**: Livewire → Controller instantiation → JSON response → render  
> (Same pattern as `Login.php`, `Register.php`, `UserProfile.php`, `UserAchievement.php`)

---

## Architecture Pattern (Existing — All Livewire Must Follow)

```php
$request = Request::create('/api/user/dashboard', 'GET', [...]);
$request->setUserResolver(fn() => Auth::user());
$controller = new UserDashboardController();
$response = $controller->index($request);
$data = json_decode($response->getContent(), true)['data'];
```

---

## Phase Overview

| Phase | Scope | Key Files |
|-------|-------|-----------|
| **1** | Database migrations + seeders | 5 migrations, 1 seeder update |
| **2** | Models & relationships | 4 new models, 1 modified |
| **3** | API controllers (ALL business logic here) | 4 new + 4 modified controllers |
| **4** | API + web routes | `api.php` + `web.php` |
| **5** | Refactor `UserDashboard.php` → consume API + add XP | 2 modified files |
| **6** | Friends Livewire + View (list layout, no sidebar, click → profile) | 2 new files |
| **7** | Profile system Livewire + View (`/user/{id}` route) | 2 rewritten files |
| **8** | Leaderboard page (podium + toggle XP/streak) | 2 new files |
| **9** | Sidebar + Dashboard XP display (top right) | 2 modified files |
| **10** | Registration update (nationality field) | 3 modified files |
| **11** | Achievement system + auto-unlock logic | 2 new models, 1 migration |

---

## Phase 1 — Database

### 1.1 Migration: `add_avatar_nationality_to_users_table`

```php
$table->string('avatar')->nullable();
$table->string('nationality')->nullable();
$table->unsignedInteger('best_streak')->default(0);
```

Update `User` model `$fillable`: add `avatar`, `nationality`, `best_streak`.

### 1.2 Migration: `create_friendships_table`

```php
Schema::create('friendships', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
    $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
    $table->timestamps();
    $table->unique(['sender_id', 'receiver_id']);
});
```

### 1.3 Migration: `create_notifications_table`

```php
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('type'); // friend_request, friend_accept, task_completed, streak_reached
    $table->json('data')->nullable();
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
    $table->index(['user_id', 'read_at']);
});
```

### 1.4 Migration: `create_activity_events_table`

```php
Schema::create('activity_events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('type'); // task_completed, streak_reached, friend_accepted
    $table->json('data')->nullable();
    $table->timestamps();
    $table->index(['user_id', 'created_at']);
});
```

### 1.5 Migration: `create_profile_achievements_table`

```php
Schema::create('profile_achievements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('display_order')->default(0);
    $table->timestamps();
    $table->unique(['user_id', 'achievement_id']);
});
```

### 1.6 Seeder: Add Test Users `user2`–`user6`

**File**: `database/seeders/DatabaseSeeder.php`

Seed format:
- `user2`: Alex Johnson, `alex@test.com`, streak 14, 8 completed tasks, nationality Indonesia
- `user3`: Sarah Chen, `sarah@test.com`, streak 7, 12 completed tasks, nationality Singapore
- `user4`: Mike Torres, `mike@test.com`, streak 3, 5 completed tasks, nationality Philippines
- `user5`: Emma Wilson, `emma@test.com`, streak 21, 15 completed tasks, nationality Malaysia
- `user6`: James Park, `james@test.com`, streak 0, 2 completed tasks, nationality South Korea

Pre-seed:
- Friendships: `user1↔user2` (accepted), `user1↔user3` (accepted), `user4→user1` (pending)
- Activity events and todos for each user (varied tasks at status 3/completed for XP calculation)
- `best_streak` populated matching `current_streak` for each user

---

## Phase 2 — Models

### New Models

| Model | Table | Key Relations |
|-------|-------|---------------|
| `Friendship` | `friendships` | `sender()`, `receiver()` |
| `AppNotification` | `notifications` | `user()`, `fromUser()` |
| `ActivityEvent` | `activity_events` | `user()` |
| `ProfileAchievement` | `profile_achievements` | `user()`, `achievement()` |

> Named `AppNotification` to avoid collision with Laravel's built-in `Notification`.

### Update `User.php`

Add to `$fillable`: `avatar`, `nationality`, `best_streak`

Add relationships:
- `sentFriendRequests()` → hasMany Friendship (`sender_id`)
- `receivedFriendRequests()` → hasMany Friendship (`receiver_id`)
- `appNotifications()` → hasMany AppNotification
- `activityEvents()` → hasMany ActivityEvent
- `profileAchievements()` → hasMany ProfileAchievement

Add helpers:
- `getFriendIds(): array`
- `getAvatarUrl(): string` — returns storage URL or null for initials fallback

---

## Phase 3 — API Controllers (ALL business logic here)

> ALL business logic lives in API controllers. Livewire reads JSON only.

### 3.1 New: `FriendController`

**File**: `app/Http/Controllers/Api/User/FriendController.php`

| Method | Route | Notes |
|--------|-------|-------|
| `index` | `GET /api/user/friends` | List accepted friends with `id`, `username`, `first_name`, `last_name`, `avatar_url`, `current_streak` |
| `search` | `GET /api/user/friends/search?q=` | Exclude self, existing friends, pending both directions |
| `sendRequest` | `POST /api/user/friends/request` | Validate no self/duplicate, create friendship + notification |
| `acceptRequest` | `POST /api/user/friends/{id}/accept` | Accept + create activity event + notification |
| `rejectRequest` | `POST /api/user/friends/{id}/reject` | Soft reject |
| `cancelRequest` | `DELETE /api/user/friends/request/{id}` | Cancel own pending outgoing request |
| `removeFriend` | `DELETE /api/user/friends/{id}` | Delete accepted friendship |
| `activity` | `GET /api/user/friends/activity` | Friend activity feed (last 20) |
| `leaderboard` | `GET /api/user/leaderboard?type=xp\|streak` | All users ranked by XP or streak; includes `podium` (top 3) and `rankings` (4+) and `current_user_rank` |
| `pendingRequests` | `GET /api/user/friends/pending` | Incoming + outgoing requests |

### 3.2 New: `NotificationController`

**File**: `app/Http/Controllers/Api/User/NotificationController.php`

| Method | Route | Notes |
|--------|-------|-------|
| `index` | `GET /api/user/notifications` | All notifications |
| `markRead` | `POST /api/user/notifications/{id}/read` | Mark one as read |
| `unreadCount` | `GET /api/user/notifications/unread-count` | Integer count for badge |

### 3.3 New: `LeaderboardController`

**File**: `app/Http/Controllers/Api/User/LeaderboardController.php`

| Method | Route | Notes |
|--------|-------|-------|
| `index` | `GET /api/user/leaderboard?type=xp\|streak` | Full leaderboard, all users |

**XP Calculation** (applied to completed todos):
```
Low priority (1)    = 10 XP
Medium priority (2) = 15 XP
High priority (3)   = 20 XP
7-day streak bonus  = 50 XP  (once per qualifying streak)
30-day streak bonus = 200 XP (once per qualifying streak)
```

**Response shape**:
```json
{
  "data": {
    "type": "xp",
    "podium": [
      { "rank": 1, "user_id": 5, "first_name": "Emma", "last_name": "Wilson", "avatar_url": "...", "value": 350 },
      { "rank": 2, "user_id": 2, "first_name": "Alex",  "last_name": "Johnson", "avatar_url": "...", "value": 280 },
      { "rank": 3, "user_id": 3, "first_name": "Sarah", "last_name": "Chen",    "avatar_url": "...", "value": 250 }
    ],
    "rankings": [
      { "rank": 4, "user_id": 4, "username": "mike", "first_name": "Mike", "last_name": "Torres", "avatar_url": "...", "value": 180 }
    ],
    "current_user_rank": { "rank": 6, "value": 90 }
  }
}
```

### 3.4 Modify: `UserProfileController`

Add new methods:

| Method | Route | Notes |
|--------|-------|-------|
| `showById` | `GET /api/user/profile/id/{id}` | View profile by user ID (for `/user/{id}` route) |
| `show` | `GET /api/user/profile/{username}` | View profile by username |
| `uploadAvatar` | `POST /api/user/avatar` | Store in `storage/app/public/avatars/{user_id}.{ext}`, return new URL |
| `updateShowcaseAchievements` | `PUT /api/user/profile/achievements` | Save 6–8 display picks + order |
| `getActivity` | `GET /api/user/activity` | Own recent activity (last 5) |

Enrich existing `index` response to include: `avatar_url`, `nationality`, `best_streak`, `weekly_xp`, `friend_count`, `friend_rank`, `tasks_completed`, `recent_activity`, `showcase_achievements`.

### 3.5 Modify: `UserDashboardController`

Add to response: `weekly_xp` field. Response must include:
- `first_name`, `current_streak`, `weekly_xp`
- `total_tasks`, `completed_tasks`, `active_tasks`, `high_priority_tasks`

### 3.6 Modify: `UserTodoController`

On task completion (status → 3):
- Create `ActivityEvent` (type: `task_completed`, data: `{ task_name, xp_earned }`)
- Trigger achievement checks (see Phase 11)

### 3.7 Modify: `AchievementController`

Add unlockable types for friends and leaderboard:
- `first_friend`, `ten_friends`, `top_3_leaderboard`

### 3.8 Modify: `AuthController`

Add `nationality` to register validation and `User::create()`.

---

## Phase 4 — Routes

### `api.php` additions

All inside existing `auth:sanctum` + `user` prefix group:

```php
// Friends
Route::prefix('friends')->group(function () {
    Route::get('/',              [FriendController::class, 'index']);
    Route::get('/search',        [FriendController::class, 'search']);
    Route::get('/pending',       [FriendController::class, 'pendingRequests']);
    Route::get('/activity',      [FriendController::class, 'activity']);
    Route::post('/request',      [FriendController::class, 'sendRequest']);
    Route::post('/{id}/accept',  [FriendController::class, 'acceptRequest']);
    Route::post('/{id}/reject',  [FriendController::class, 'rejectRequest']);
    Route::delete('/{id}',       [FriendController::class, 'removeFriend']);
    Route::delete('/request/{id}', [FriendController::class, 'cancelRequest']);
});

// Leaderboard
Route::get('/leaderboard', [LeaderboardController::class, 'index']);

// Notifications
Route::prefix('notifications')->group(function () {
    Route::get('/',              [NotificationController::class, 'index']);
    Route::get('/unread-count',  [NotificationController::class, 'unreadCount']);
    Route::post('/{id}/read',    [NotificationController::class, 'markRead']);
});

// Profile (additions to existing prefix)
Route::prefix('profile')->group(function () {
    Route::get('/id/{id}',       [UserProfileController::class, 'showById']);  // NEW: by user ID
    Route::get('/{username}',    [UserProfileController::class, 'show']);
    Route::put('/achievements',  [UserProfileController::class, 'updateShowcaseAchievements']);
});

// Avatar + Activity
Route::post('/avatar',   [UserProfileController::class, 'uploadAvatar']);
Route::get('/activity',  [UserProfileController::class, 'getActivity']);
```

### `web.php` additions/changes

```php
// Inside auth + user middleware group:
Route::get('/friends',              fn() => view('user.friends.index'));
Route::get('/leaderboard',          fn() => view('user.leaderboard.index'));
Route::get('/profile',              fn() => view('user.profile.index'));
Route::get('/profile/{username}',   fn($username) => view('user.profile.index', compact('username')));
Route::get('/{id}',                 fn($id) => view('user.profile.index', ['userId' => $id]));
// ↑ /user/{id} → pass numeric ID to profile view → Livewire resolves via /api/user/profile/id/{id}
```

Remove old `/user/profile` route (replaced above).

> **Note on `/user/{id}`**: The profile Livewire component checks for a numeric `$userId` prop first, then `$username`, then falls back to own profile.

---

## Phase 5 — Refactor `UserDashboard.php` + XP Display

### Fix: All Livewire Must Consume API

**Wrong (direct Eloquent)**:
```php
return Todo::where('user_id', Auth::id())->count();
```

**Right (API pattern)**:
```php
private function callController(string $controllerClass, string $method, string $httpMethod = 'GET', array $data = []): array
{
    $request = Request::create('/api/user/dashboard', $httpMethod, $data);
    $request->setUserResolver(fn() => Auth::user());
    $controller = app($controllerClass);
    $response = $controller->$method($request);
    return json_decode($response->getContent(), true)['data'] ?? [];
}
```

Apply this pattern to:
- `UserDashboard.php` — dashboard stats, task list
- `UserFriends.php` — all friend operations
- `UserProfile.php` — profile data, avatar, achievements
- `UserLeaderboard.php` — leaderboard data
- Any other Livewire component using Eloquent directly

### Dashboard Header XP (top right)

In `UserDashboard.php`:
```php
public int $weeklyXp = 0;

public function mount() {
    $data = $this->callController(UserDashboardController::class, 'index');
    $this->weeklyXp = $data['weekly_xp'] ?? 0;
    // ... rest of mount
}
```

In `user_dashboard.blade.php`:
```html
<header class="db-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h1 class="db-header-title">Dashboard</h1>
    <div class="db-xp-display" style="display:flex; align-items:center; gap:6px;">
        <!-- Lightning bolt SVG icon, color: var(--db-medium) / #f59e0b -->
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="color:#f59e0b">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
        </svg>
        <span style="font-size:16px; font-weight:600; color:var(--db-text);">{{ $weeklyXp }} XP</span>
    </div>
</header>
```

Also replace hardcoded `$leaderboard` array — load real data from `LeaderboardController::index` via API call.

---

## Phase 6 — Friends Page

### Livewire: `UserFriends.php`

**File**: `app/Livewire/UserFriends.php`

Consumes API:
- `GET /api/user/friends` — friend list
- `GET /api/user/friends/search?q=` — search users
- `GET /api/user/friends/pending` — pending requests
- `POST /api/user/friends/request` — send request
- `POST /api/user/friends/{id}/accept` — accept
- `POST /api/user/friends/{id}/reject` — reject
- `DELETE /api/user/friends/request/{id}` — cancel outgoing
- `DELETE /api/user/friends/{id}` — remove friend

Properties:
```php
public array $friends = [];
public array $searchResults = [];
public array $pendingIncoming = [];
public array $pendingOutgoing = [];
public string $searchQuery = '';
public bool $showAddFriendModal = false;
public bool $showPendingModal = false;
public int $pendingCount = 0;
```

### View: `user_friends.blade.php`

**Layout** — no right sidebar, full-width list:

```
┌──────────────────────────────────────────────────────────────────┐
│ sidebar │  Friends                                                │
│         │  ┌───────────────────────────────────────────────────┐ │
│         │  │ [🔍 Search friends...]  [+ Add Friend] [Pending 3]│ │
│         │  └───────────────────────────────────────────────────┘ │
│         │─────────────────────────────────────────────────────── │
│         │  ┌───────────────────────────────────────────────────┐ │
│         │  │ [avatar] Alex Johnson   @alexj      🔥 14  [✕]  │ → /user/5
│         │  │ [avatar] Sarah Chen     @sarah      🔥 7   [✕]  │ → /user/3
│         │  │ [avatar] Emma Wilson    @emma       🔥 21  [✕]  │ → /user/6
│         │  └───────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────┘
```

**Row behavior**:
- Entire row is clickable → `wire:click="goToProfile({{ $friend['id'] }})"` → redirects to `/user/{id}`
- Remove button `[✕]` stops propagation, calls `removeFriend($id)`

**Row markup pattern**:
```html
<div class="friend-row" wire:click="goToProfile({{ $friend['id'] }})" style="cursor:pointer;">
    <img src="{{ $friend['avatar_url'] ?? '' }}" class="friend-avatar" />
    <div class="friend-info">
        <span class="friend-name">{{ $friend['first_name'] }} {{ $friend['last_name'] }}</span>
        <span class="friend-username">@{{ $friend['username'] }}</span>
    </div>
    <div class="friend-streak">🔥 {{ $friend['current_streak'] }}</div>
    <button wire:click.stop="removeFriend({{ $friend['id'] }})" class="friend-remove">✕</button>
</div>
```

**Header bar** (search + buttons side by side):
```html
<div class="friends-toolbar">
    <input wire:model.live.debounce.300ms="searchQuery" placeholder="Search friends..." />
    <button wire:click="$set('showAddFriendModal', true)">+ Add Friend</button>
    <button wire:click="$set('showPendingModal', true)">
        Pending @if($pendingCount > 0) <span class="badge">{{ $pendingCount }}</span> @endif
    </button>
</div>
```

**Add Friend Modal**: Search input → results list → each result has `[Add Friend]` or `[Pending]` button.

**Pending Modal**: Two tabs/sections — Incoming (Accept / Reject) + Outgoing (Cancel).

---

## Phase 7 — Profile System

### Routes

- `/user/profile` → own profile
- `/user/profile/{username}` → view by username
- `/user/{id}` → view by numeric ID (used by friend list click)

### Livewire: `UserProfile.php` (full rewrite)

**File**: `app/Livewire/UserProfile.php`

```php
public ?int $userId = null;          // from /user/{id}
public ?string $profileUsername = null; // from /user/profile/{username}
public bool $isOwnProfile = false;
public array $profileData = [];
// Modals
public bool $showEditProfileModal = false;
public bool $showAvatarModal = false;
public bool $showAchievementsModal = false;
// Edit fields
public string $editFirstName = '';
public string $editLastName = '';
public string $editNationality = '';
```

Mount logic:
```php
public function mount($userId = null, $username = null) {
    if ($userId) {
        // GET /api/user/profile/id/{userId}
    } elseif ($username) {
        // GET /api/user/profile/{username}
    } else {
        // GET /api/user/profile (own)
    }
    $this->isOwnProfile = (auth()->id() === ($this->profileData['id'] ?? null));
}
```

Actions:
- `saveProfile()` → `PUT /api/user/profile`
- `uploadAvatar($file)` → `POST /api/user/avatar`
- `saveShowcaseAchievements($selected)` → `PUT /api/user/profile/achievements`
- `addFriend()` / `removeFriend()` → friend API endpoints

### View: `user_profile.blade.php` (full rewrite)

**Desktop layout — NO vertical scrolling on 1920×1080:**

```
┌────────────────────────────────────────────────────────────────────┐
│ sidebar │  TOP: 2 cards side by side (~25% width each)             │
│         │  ┌──────────────────────┐ ┌─────────────────────────┐   │
│         │  │ [avatar]             │ │  🔥 Current Streak      │   │
│         │  │ John Doe             │ │       12                │   │
│         │  │ @johndoe   [Edit]    │ │  Best Streak: 21        │   │
│         │  │ 🇮🇩 Indonesia        │ │                         │   │
│         │  └──────────────────────┘ └─────────────────────────┘   │
│         │                                                          │
│         │  MIDDLE: 4 stat cards + Recent Activity                  │
│         │  ┌──────┐┌──────┐┌──────┐┌──────┐ ┌──────────────────┐ │
│         │  │Tasks ││Best  ││Weekly││Friend│ │ Recent Activity  │ │
│         │  │  42  ││  21  ││ 350  ││  #2  │ │ ✓ Finish Assign │ │
│         │  │Compl.││Streak││  XP  ││ Rank │ │ ✓ Study Laravel │ │
│         │  └──────┘└──────┘└──────┘└──────┘ │ ✓ Prep Pres.   │ │
│         │                                    └──────────────────┘ │
│         │  BOTTOM: Achievement Showcase (6–8 slots grid)          │
│         │  ┌────┐┌────┐┌────┐┌────┐┌────┐┌────┐┌────┐┌────┐     │
│         │  │ 🏆 ││ 🔥 ││ 👥 ││    ││    ││    ││    ││    │     │
│         │  │1st ││7Day││Frd.││ —  ││ —  ││ —  ││ —  ││ —  │     │
│         │  └────┘└────┘└────┘└────┘└────┘└────┘└────┘└────┘     │
└────────────────────────────────────────────────────────────────────┘
```

**Own profile**: Edit pencil icon on profile card, clickable avatar for upload, edit icon on achievements.  
**Other user's profile**: View only + `[+ Add Friend]` or `[✓ Friends]` or `[Pending]` button in top card.  
**Empty achievement slots**: Muted placeholder box with dashed border.

**Avatar handling**:
- Upload → `POST /api/user/avatar` → stored as `storage/app/public/avatars/{user_id}.{ext}`
- Display: `Storage::url('avatars/...')` or fallback to generated initials circle (CSS background + initials)
- Run `php artisan storage:link`

**Modals** (no separate pages, all inline):
1. **Edit Profile Modal**: First Name, Last Name, Nationality fields → `PUT /api/user/profile`
2. **Change Avatar Modal**: File input + preview → `POST /api/user/avatar`
3. **Edit Achievements Modal**: Grid of unlocked achievements (checkboxes) → max 8 selectable → `PUT /api/user/profile/achievements`

---

## Phase 8 — Leaderboard Page

### Livewire: `UserLeaderboard.php`

**File**: `app/Livewire/UserLeaderboard.php`

```php
public string $type = 'xp'; // 'xp' or 'streak'
public array $podium = [];
public array $rankings = [];
public array $currentUserRank = [];

public function mount() { $this->loadLeaderboard(); }

public function switchType(string $type) {
    $this->type = $type;
    $this->loadLeaderboard();
}

private function loadLeaderboard() {
    $data = /* GET /api/user/leaderboard?type={$this->type} */;
    $this->podium = $data['podium'];
    $this->rankings = $data['rankings'];
    $this->currentUserRank = $data['current_user_rank'];
}
```

### View: `user_leaderboard.blade.php`

**Layout**:

```
┌──────────────────────────────────────────────────────────────┐
│ sidebar │  Leaderboard                                        │
│         │                                                     │
│         │         PODIUM (top 3 — centered)                  │
│         │                                                     │
│         │             [avatar]                                │
│         │              Emma W.                                │
│         │             350 XP ← grayed                        │
│         │          🥇─────────🥇                             │
│         │  [avtr]  │         │  [avtr]                       │
│         │  Alex J. │  #1     │  Sarah C.                     │
│         │  280 XP  │  tall   │  250 XP                       │
│         │  🥈──────┤  block  ├──────🥉                       │
│         │          │         │                               │
│         │          └─────────┘                               │
│         │                                                     │
│         │       [  XP  ]  [ Streak ]  ← side-by-side toggle  │
│         │                                                     │
│         │  RANKINGS (#4 onward)                               │
│         │  #4  [avtr] Mike Torres    @mike    180 XP          │
│         │  #5  [avtr] James Park     @james    90 XP          │
│         │  ...                                                │
└──────────────────────────────────────────────────────────────┘
```

**Podium details**:
- Three columns: `#2 left` | `#1 center (taller block)` | `#3 right`
- Above each podium block: user avatar circle image
- Below avatar: `first_name + last_name` (not username)
- Below name: XP or streak value in grayed-out muted text
- Medal emoji above avatar: 🥇 🥈 🥉

**Toggle buttons** (side-by-side, centered, below podium):
```html
<div class="leaderboard-toggle">
    <button wire:click="switchType('xp')"
            class="{{ $type === 'xp' ? 'active' : '' }}">XP</button>
    <button wire:click="switchType('streak')"
            class="{{ $type === 'streak' ? 'active' : '' }}">Streak</button>
</div>
```
Active state: filled background (`--db-accent`). Inactive: outlined/muted.

**Rankings list** (#4+): Avatar + full name + @username + value (XP or streak days). Highlight current user's row.

---

## Phase 9 — Sidebar + Dashboard Updates

### `sidebar.blade.php`

Add Friends nav item between Achievements and Leaderboard:
```html
<a href="/user/friends" class="db-nav-item {{ Request::is('user/friends') ? 'active' : '' }}">
    <!-- People/group SVG icon -->
    <svg>...</svg>
</a>
```

Fix Leaderboard link: change `href="#"` → `href="/user/leaderboard"`.

### Dashboard `user_dashboard.blade.php`

- Add XP display to header top-right (see Phase 5 markup)
- Replace hardcoded `$leaderboard` array with real API data from `LeaderboardController`

---

## Phase 10 — Registration Update (Nationality)

### `Register.php`

```php
public string $nationality = '';
// Add to rules:
'nationality' => 'nullable|string|max:100'
```

### `register.blade.php`

Add nationality input field in registration form (after last name, before email or similar).  
Can be a text input or a country dropdown.

### `AuthController::register`

Add `nationality` to `$request->validate()` and `User::create([...])`.

---

## Phase 11 — Achievement System

### Migrations

**`create_achievements_table`**:
```php
Schema::create('achievements', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('description');
    $table->string('icon'); // emoji or icon identifier
    $table->string('requirement_type'); // tasks_completed, streak_days, friends_count, leaderboard_rank
    $table->unsignedInteger('requirement_value');
    $table->timestamps();
});
```

**`create_user_achievements_table`**:
```php
Schema::create('user_achievements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
    $table->timestamp('unlocked_at');
    $table->timestamps();
    $table->unique(['user_id', 'achievement_id']);
});
```

### Achievement Catalog (seeded)

| Name | Icon | Type | Value |
|------|------|------|-------|
| First Task | ✅ | tasks_completed | 1 |
| 10 Tasks Completed | 📋 | tasks_completed | 10 |
| 100 Tasks Completed | 🏅 | tasks_completed | 100 |
| 7 Day Streak | 🔥 | streak_days | 7 |
| 30 Day Streak | 💥 | streak_days | 30 |
| 100 Day Streak | 🌟 | streak_days | 100 |
| First Friend | 👋 | friends_count | 1 |
| 10 Friends | 👥 | friends_count | 10 |
| Top 3 Leaderboard | 🏆 | leaderboard_rank | 3 |

### Auto-unlock Logic

Create `AchievementService::checkAndUnlock(User $user, string $triggerType)`.

Call after:
- Task completed → check `tasks_completed` achievements
- Streak updated → check `streak_days` achievements
- Friend accepted → check `friends_count` achievements
- Leaderboard recalculated → check `leaderboard_rank` achievements

Users cannot manually create achievements. They may only select which unlocked ones appear on their profile showcase (up to 8, via `profile_achievements` table).

### `UserAchievement.php` Model

```php
class UserAchievement extends Model {
    protected $fillable = ['user_id', 'achievement_id', 'unlocked_at'];
    protected $casts = ['unlocked_at' => 'datetime'];
    public function achievement() { return $this->belongsTo(Achievement::class); }
    public function user() { return $this->belongsTo(User::class); }
}
```

---

## File Summary

### New Files (20)

| # | File |
|---|------|
| 1 | `database/migrations/..._add_avatar_nationality_to_users.php` |
| 2 | `database/migrations/..._create_friendships_table.php` |
| 3 | `database/migrations/..._create_notifications_table.php` |
| 4 | `database/migrations/..._create_activity_events_table.php` |
| 5 | `database/migrations/..._create_profile_achievements_table.php` |
| 6 | `database/migrations/..._create_achievements_table.php` |
| 7 | `database/migrations/..._create_user_achievements_table.php` |
| 8 | `app/Models/Friendship.php` |
| 9 | `app/Models/AppNotification.php` |
| 10 | `app/Models/ActivityEvent.php` |
| 11 | `app/Models/ProfileAchievement.php` |
| 12 | `app/Models/Achievement.php` |
| 13 | `app/Models/UserAchievement.php` |
| 14 | `app/Http/Controllers/Api/User/FriendController.php` |
| 15 | `app/Http/Controllers/Api/User/NotificationController.php` |
| 16 | `app/Http/Controllers/Api/User/LeaderboardController.php` |
| 17 | `app/Services/AchievementService.php` |
| 18 | `app/Livewire/UserFriends.php` |
| 19 | `app/Livewire/UserLeaderboard.php` |
| 20 | `resources/views/livewire/user_friends.blade.php` |
| 21 | `resources/views/livewire/user_leaderboard.blade.php` |

### Modified Files (15)

| # | File | Change |
|---|------|--------|
| 1 | `app/Models/User.php` | Add fillable, relationships, helpers |
| 2 | `app/Http/Controllers/Api/AuthController.php` | Add nationality to register |
| 3 | `app/Http/Controllers/Api/User/UserProfileController.php` | Add showById, avatar, achievements, activity |
| 4 | `app/Http/Controllers/Api/User/UserDashboardController.php` | Add weekly_xp to response |
| 5 | `app/Http/Controllers/Api/User/UserTodoController.php` | Emit ActivityEvent + achievement check on complete |
| 6 | `app/Http/Controllers/Api/User/AchievementController.php` | Add friend/leaderboard types |
| 7 | `app/Livewire/UserDashboard.php` | Consume API fully, add weeklyXp prop |
| 8 | `app/Livewire/UserProfile.php` | Full rewrite for new profile system |
| 9 | `app/Livewire/Register.php` | Add nationality field |
| 10 | `resources/views/livewire/user_dashboard.blade.php` | XP in header, real leaderboard |
| 11 | `resources/views/livewire/user_profile.blade.php` | Full rewrite |
| 12 | `resources/views/livewire/register.blade.php` | Add nationality input |
| 13 | `resources/views/components/sidebar.blade.php` | Add Friends link, fix Leaderboard link |
| 14 | `routes/api.php` | All new API routes |
| 15 | `routes/web.php` | Friends, leaderboard, `/user/{id}`, profile routes |
| 16 | `database/seeders/DatabaseSeeder.php` | Add user2–user6, friendships, activity |

---

## Execution Order

```
Phase 1   → migrations + seeders  →  php artisan migrate:fresh --seed
Phase 2   → models
Phase 3   → API controllers (all business logic)
Phase 4   → routes (api.php + web.php)
Phase 5   → refactor UserDashboard.php → consume API + XP header
Phase 6   → Friends page (Livewire + view, list layout, click → /user/{id})
Phase 7   → Profile system (Livewire + view, /user/{id} + /user/profile/{username})
Phase 8   → Leaderboard (Livewire + view, podium + XP/streak toggle)
Phase 9   → Sidebar (Friends link + fix Leaderboard link)
Phase 10  → Registration nationality field
Phase 11  → Achievement catalog + auto-unlock service
```

> [!CAUTION]
> Phase 1 uses `migrate:fresh --seed` which wipes the database. Back up any important data first.

---

## Quick Reference: What Changed vs v1

| # | Change | Where |
|---|--------|-------|
| 1 | All Livewire must consume API — explicitly called out as fix | Phases 5–9 |
| 2 | Friend list is a full-width row list (no right sidebar) | Phase 6 |
| 3 | Clicking a friend row → `/user/{id}` (numeric ID route) | Phase 6 |
| 4 | Add Friend + Pending buttons are side-by-side next to search | Phase 6 |
| 5 | Profile accessible via `/user/{id}` (numeric) in addition to username | Phase 4, 7 |
| 6 | `GET /api/user/profile/id/{id}` endpoint added | Phase 3.4 |
| 7 | Leaderboard has podium (top 3 with avatar, full name, grayed value) | Phase 8 |
| 8 | Leaderboard toggle (XP / Streak) side-by-side buttons, centered | Phase 8 |
| 9 | XP displayed top-right of dashboard header | Phase 5, 9 |
| 10 | Seeders added user2–user6 with friendships for testing | Phase 1.6 |
| 11 | Achievement system formalized with catalog + auto-unlock service | Phase 11 |
| 12 | `AchievementService` introduced for clean unlock logic | Phase 11 |
