<div class="db-layout">
    @if ($profileData && $profileData['id'] == auth()->user()->id)
        <x-sidebar active="profile"/>
    @else
        <x-sidebar active="friends"/>
    @endif

    <div class="db-root">
        @if(!empty($profileData))
        {{-- ═══ TOP SECTION: Profile Card + Streak Card ═══ --}}
        <div class="pf-top">
            {{-- Profile Card --}}
            <div class="pf-card pf-profile-card">
                <div class="pf-profile-main">
                    <div class="pf-avatar-wrap">
                        @php
                            $borderColor = $profileData['active_avatar_border']['color'] ?? '#22c55e';
                            $isGradient = str_starts_with($borderColor, 'linear-gradient');
                        @endphp
                        @if(!empty($profileData['avatar_url']))
                            <img src="{{ $profileData['avatar_url'] }}" class="pf-avatar-img" alt="Avatar" @if(!$isGradient) style="border-color: {{ $borderColor }};" @else style="border: none; box-shadow: 0 0 0 4px {{ $borderColor }};" @endif>
                        @else
                            @php $initials = strtoupper(substr($profileData['first_name'] ?? '', 0, 1) . substr($profileData['last_name'] ?? '', 0, 1)); @endphp
                            @if($profileData['gender'] === 'male')
                                <div class="pf-avatar-placeholder avatar-placeholder-male" @if(!$isGradient) style="border-color: {{ $borderColor }};" @else style="border: none; box-shadow: 0 0 0 4px {{ $borderColor }};" @endif>{{ $initials }}</div>
                            @elseif($profileData['gender'] === 'female')
                                <div class="pf-avatar-placeholder avatar-placeholder-female" @if(!$isGradient) style="border-color: {{ $borderColor }};" @else style="border: none; box-shadow: 0 0 0 4px {{ $borderColor }};" @endif>{{ $initials }}</div>
                            @else
                                <div class="pf-avatar-placeholder" @if(!$isGradient) style="border-color: {{ $borderColor }};" @else style="border: none; box-shadow: 0 0 0 4px {{ $borderColor }};" @endif>{{ $initials }}</div>
                            @endif
                        @endif
                        @if($isOwnProfile)
                            <button wire:click="openAvatarModal" class="pf-avatar-edit" title="Change avatar">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                            </button>
                        @endif
                    </div>
                    <div class="pf-identity">
                        <h2 class="pf-name">{{ $profileData['full_name'] }}</h2>
                        <p class="pf-username"><span>@</span>{{ $profileData['username'] }}</p>
                        @if(!empty($profileData['nationality']))
                            <p class="pf-nationality">{{ $profileData['nationality'] }}</p>
                        @endif
                    </div>
                </div>
                @if($isOwnProfile)
                    <button wire:click="openEditProfileModal" class="pf-edit-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                        Edit
                    </button>
                @else
                    {{-- Friend actions --}}
                    @if($profileData['friendship_status'] === 'accepted')
                        <button wire:click="openConfirmModal('removeFriend', 'Remove Friend?', 'Are you sure you want to remove this friend?')" class="pf-edit-btn pf-btn-danger">Remove Friend</button>
                    @elseif($profileData['friendship_status'] === 'pending')
                        <span class="pf-pending-label">Request Pending</span>
                    @else
                        <button wire:click="sendFriendRequest" class="pf-edit-btn pf-btn-accent">Add Friend</button>
                    @endif
                @endif
            </div>

            {{-- Streak Card --}}
            <div class="pf-card pf-streak-card">
                <div class="pf-streak-main">
                    <span class="pf-streak-emoji">🔥</span>
                    <span class="pf-streak-number">{{ $profileData['streak_count'] ?? 0 }}</span>
                </div>
                <div class="pf-streak-label">CURRENT STREAK</div>
                <div class="pf-streak-best">Best: {{ $profileData['best_streak'] ?? 0 }} days</div>
            </div>
        </div>

        {{-- ═══ MIDDLE SECTION: Stats + Activity ═══ --}}
        <div class="pf-middle">
            <div class="pf-stats-row">
                <div class="pf-card pf-stat-card">
                    <div class="pf-stat-label">Tasks Completed</div>
                    <div class="pf-stat-line"></div>
                    <div class="pf-stat-val">{{ $profileData['tasks_completed'] ?? 0 }}</div>
                </div>
                <div class="pf-card pf-stat-card">
                    <div class="pf-stat-label">Best Streak</div>
                    <div class="pf-stat-line"></div>
                    <div class="pf-stat-val">{{ $profileData['best_streak'] ?? 0 }}</div>
                </div>
                <div class="pf-card pf-stat-card">
                    <div class="pf-stat-label">Weekly XP</div>
                    <div class="pf-stat-line"></div>
                    <div class="pf-stat-val">{{ $profileData['weekly_xp'] ?? 0 }}</div>
                </div>
                <div class="pf-card pf-stat-card">
                    <div class="pf-stat-label">Rank</div>
                    <div class="pf-stat-line"></div>
                    <div class="pf-stat-val">#{{ $profileData['friend_rank'] ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- ═══ BOTTOM SECTION: Achievement Showcase ═══ --}}
        <div class="pf-bottom">
            <div class="pf-card pf-achievements-card">
                <div class="pf-ach-header">
                    <h3 class="pf-section-title">Achievements</h3>
                    @if($isOwnProfile)
                        <button wire:click="openAchievementsModal" class="pf-edit-btn pf-btn-sm">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                            Edit
                        </button>
                    @endif
                </div>
                <div class="pf-ach-grid">
                    @for($i = 0; $i < 8; $i++)
                        @if(isset($profileData['showcase_achievements'][$i]))
                            @php $ach = $profileData['showcase_achievements'][$i]; @endphp
                            <div class="pf-ach-slot pf-ach-filled" title="{{ $ach['description'] ?? $ach['name'] }}">
                                @if(!empty($ach['icon']))
                                    <img src="{{ asset('AchievementBadges/' . $ach['icon']) }}" class="pf-ach-icon" alt="{{ $ach['name'] }}">
                                @else
                                    <span class="pf-ach-text">🏆</span>
                                @endif
                                <span class="pf-ach-name">{{ $ach['name'] }}</span>
                            </div>
                        @else
                            <div class="pf-ach-slot pf-ach-empty">
                                <span class="pf-ach-text">—</span>
                            </div>
                        @endif
                    @endfor
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="pf-card pf-activity-card">
                <h3 class="pf-section-title">Recent Activity</h3>
                <div class="pf-activity-list">
                    @forelse($profileData['recent_activity'] ?? [] as $activity)
                        <div class="pf-activity-item">
                            @if($activity['type'] === 'task_completed')
                                <span class="pf-activity-icon">✅</span>
                                <span>Completed "{{ $activity['data']['task_name'] ?? 'a task' }}"</span>
                            @elseif($activity['type'] === 'streak_reached')
                                <span class="pf-activity-icon">🔥</span>
                                <span>Reached {{ $activity['data']['streak'] ?? '' }}-day streak</span>
                            @elseif($activity['type'] === 'friend_accepted')
                                <span class="pf-activity-icon">🤝</span>
                                <span>Became friends with {{ $activity['data']['friend_name'] ?? 'someone' }}</span>
                            @endif
                            <span class="pf-activity-time">{{ $activity['created_at'] }}</span>
                        </div>
                    @empty
                        <div class="pf-empty-sm">No recent activity.</div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ═══ EDIT PROFILE MODAL ═══ --}}
        @if($showEditProfileModal)
        <div wire:key="modal-edit-profile" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="$set('showEditProfileModal', false)">
            <div class="w-full max-w-md bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">Edit Profile</h2>
                    <button type="button" wire:click="$set('showEditProfileModal', false)" class="text-gray-400 hover:text-white cursor-pointer">✕</button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-400 block mb-1">First Name</label>
                            <input type="text" wire:model="editFirstName" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white">
                        </div>
                        <div>
                            <label class="text-sm text-gray-400 block mb-1">Last Name</label>
                            <input type="text" wire:model="editLastName" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-400 block mb-1">Gender</label>
                            <select wire:model="editGender" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white">
                                <option value="" disabled hidden>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-400 block mb-1">Nationality</label>
                            <select wire:model="editNationality" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white">
                                <option value="" disabled hidden>Select Nationality</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="border-t border-[#2a2d3e] pt-4 mt-4">
                        <h3 class="text-sm font-bold text-gray-300 mb-4">Change Password (Optional)</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-400 block mb-1">Current Password</label>
                                <input type="password" wire:model="editOldPassword" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white">
                            </div>
                            <div>
                                <label class="text-sm text-gray-400 block mb-1">New Password</label>
                                <input type="password" wire:model="editNewPassword" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white">
                            </div>
                            <div>
                                <label class="text-sm text-gray-400 block mb-1">Confirm New Password</label>
                                <input type="password" wire:model="editNewPasswordConfirmation" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showEditProfileModal', false)" class="px-4 py-2 rounded-lg border border-[#2a2d3e] text-gray-300 cursor-pointer hover:bg-[#2a2d3e]">Cancel</button>
                    <button wire:click="saveProfile" class="px-4 py-2 rounded-lg bg-[#22c55e] text-white cursor-pointer hover:bg-[#1ea951]">Save</button>
                </div>
            </div>
        </div>
        @endif

        {{-- ═══ CONFIRMATION MODAL ═══ --}}
        @if($showConfirmModal)
        <div wire:key="modal-confirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="$set('showConfirmModal', false)">
            <div class="w-full max-w-sm bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">
                <h2 class="text-xl font-bold text-white mb-2">{{ $confirmTitle }}</h2>
                <p class="text-gray-400 mb-6">{{ $confirmMessage }}</p>
                <div class="flex gap-3 justify-end">
                    <button wire:click="$set('showConfirmModal', false)" class="px-4 py-2 rounded-lg border border-[#2a2d3e] text-gray-300 cursor-pointer hover:bg-[#2a2d3e]">Cancel</button>
                    <button wire:click="executeConfirmAction" class="px-4 py-2 rounded-lg bg-[#ef4444] text-white cursor-pointer hover:bg-[#dc2626]">Confirm</button>
                </div>
            </div>
        </div>
        @endif

        {{-- ═══ AVATAR MODAL ═══ --}}
        @if($showAvatarModal)
        <div wire:key="modal-avatar" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="$set('showAvatarModal', false)">
            <div class="w-full max-w-md bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">Change Avatar</h2>
                    <button type="button" wire:click="$set('showAvatarModal', false)" class="text-gray-400 hover:text-white cursor-pointer">✕</button>
                </div>
                <div class="space-y-4">
                    @if($avatarUpload)
                        <div class="flex justify-center">
                            <img src="{{ $avatarUpload->temporaryUrl() }}" class="w-32 h-32 rounded-full object-cover border-2 border-[#22c55e]">
                        </div>
                    @endif
                    <input type="file" wire:model="avatarUpload" accept="image/*" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white file:bg-[#2a2d3e] file:border-0 file:text-white file:rounded file:mr-3 file:cursor-pointer">
                    @error('avatarUpload') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showAvatarModal', false)" class="px-4 py-2 rounded-lg border border-[#2a2d3e] text-gray-300 cursor-pointer hover:bg-[#2a2d3e]">Cancel</button>
                    <button wire:click="saveAvatar" class="px-4 py-2 rounded-lg bg-[#22c55e] text-white cursor-pointer hover:bg-[#1ea951]" @if(!$avatarUpload) disabled @endif>Upload</button>
                </div>
            </div>
        </div>
        @endif

        {{-- ═══ ACHIEVEMENTS SHOWCASE MODAL ═══ --}}
        @if($showAchievementsModal)
        <div wire:key="modal-achievements" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="$set('showAchievementsModal', false)">
            <div class="w-full max-w-lg bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-white">Select Showcase Achievements</h2>
                    <button type="button" wire:click="$set('showAchievementsModal', false)" class="text-gray-400 hover:text-white cursor-pointer">✕</button>
                </div>
                <p class="text-sm text-gray-400 mb-4">Select up to 8 unlocked achievements to display on your profile.</p>
                <div class="grid grid-cols-2 gap-3 max-h-[300px] overflow-y-auto mb-6">
                    @foreach($allAchievements as $ach)
                        @php $isSelected = in_array($ach['id'], $selectedAchievementIds); @endphp
                        <button
                            wire:click="toggleAchievement({{ $ach['id'] }})"
                            class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all text-left
                                {{ $isSelected ? 'border-[#22c55e] bg-[#22c55e10]' : 'border-[#2a2d3e] bg-[#1f2235] hover:border-[#3a3d50]' }}"
                        >
                            @if(!empty($ach['icon']))
                                <img src="{{ asset('AchievementBadges/' . $ach['icon']) }}" class="w-8 h-8 object-contain">
                            @else
                                <span class="text-lg">🏆</span>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-white truncate">{{ $ach['name'] }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $ach['description'] ?? '' }}</div>
                            </div>
                            @if($isSelected)
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    @endforeach
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-400">{{ count($selectedAchievementIds) }}/8 selected</span>
                    <div class="flex gap-3">
                        <button wire:click="$set('showAchievementsModal', false)" class="px-4 py-2 rounded-lg border border-[#2a2d3e] text-gray-300 cursor-pointer hover:bg-[#2a2d3e]">Cancel</button>
                        <button wire:click="saveShowcaseAchievements" class="px-4 py-2 rounded-lg bg-[#22c55e] text-white cursor-pointer hover:bg-[#1ea951]">Save</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
:root {
    --db-bg:#0f1117; --db-surface:#1a1d27; --db-surface2:#1f2235;
    --db-border:#2a2d3e; --db-text:#e2e8f0; --db-muted:#64748b;
    --db-accent:#22c55e; --db-accent2:#3b82f6; --db-radius:12px; --db-radius-sm:8px;
}
.db-layout { display:flex; height:100vh; background:var(--db-bg); overflow:hidden; font-family:'Inter','Segoe UI',system-ui,sans-serif; }
.db-sidebar-nav { width:80px; background:#0f1117; border-right:1px solid var(--db-border); display:flex; flex-direction:column; align-items:center; padding:32px 0; justify-content:space-between; flex-shrink:0; }
.db-logo { width:50px; height:50px; object-fit:contain; }
.db-sidebar-middle,.db-sidebar-bottom { display:flex; flex-direction:column; gap:32px; }
.db-nav-item { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--db-muted); transition:all .2s; }
.db-nav-item:hover { color:var(--db-text); background:var(--db-surface); }
.db-nav-item.active { color:var(--db-accent); background:rgba(34,197,94,0.1); box-shadow:0 0 15px rgba(34,197,94,0.2); }
.db-root{
    flex:1;
    display:grid;
    grid-template-columns: 7.8fr 2.2fr;
    grid-template-rows: 280px 170px 1fr;
    gap: 16px;
    overflow: hidden;
    padding: 24px;
}

/* ── PROFILE ── */
.pf-card { background:var(--db-surface); border:1px solid var(--db-border); border-radius:var(--db-radius); padding:24px; }

.pf-top{
    display:contents;
}

.pf-profile-card{
    grid-column:1;
    grid-row:1;

    display:flex;
    align-items:center;
    justify-content:space-between;

    height:100%;
    padding:40px;
}
.pf-profile-main { display:flex; align-items:center; gap:24px; }

.pf-avatar-wrap { position:relative; flex-shrink:0; }
.pf-avatar-img { width:180px; height:180px; border-radius:50%; object-fit:cover; border:3px solid var(--db-accent); }
.pf-avatar-placeholder { width:100px; height:100px; border-radius:50%; background:var(--db-surface2); border:3px solid var(--db-accent); display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:700; color:var(--db-accent); }
.pf-avatar-img,
.pf-avatar-placeholder{
    width:180px;
    height:180px;
}
.avatar-placeholder-male { color:#3b82f6; }
.avatar-placeholder-female { color:#ec4899; }
.pf-avatar-edit { 
    position:absolute; 
    bottom:8px; 
    right:8px; 
    width:40px; 
    height:40px; 
    background:#1a1d27; 
    border:1px solid var(--db-border); 
    border-radius:50%; 
    display:flex; 
    align-items:center; 
    justify-content:center; 
    cursor:pointer; 
    color:var(--db-muted); 
    transition:all .2s; 
    z-index:10;
}
.pf-avatar-edit:hover { color:var(--db-accent); border-color:var(--db-accent); background:#2a2d3e; }

.pf-identity { display:flex; flex-direction:column; }
.pf-name { font-size:48px; font-weight:700; margin:0 0 2px 0; color:var(--db-text); }
.pf-username { font-size:20px; color:var(--db-muted); margin:0 0 4px 0; }
.pf-nationality { font-size:14px; color:var(--db-muted); margin:0; }

.pf-edit-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; border:1px solid var(--db-border); background:var(--db-surface2); color:var(--db-text); font-size:13px; font-weight:500; cursor:pointer; transition:all .2s; }
.pf-edit-btn:hover { border-color:var(--db-accent); color:var(--db-accent); }
.pf-btn-sm { padding:5px 10px; font-size:12px; }
.pf-btn-accent { background:var(--db-accent); color:#fff; border-color:var(--db-accent); }
.pf-btn-accent:hover { background:#1ea951; }
.pf-btn-danger { border-color:#ef4444; color:#ef4444; }
.pf-btn-danger:hover { background:#ef444420; }
.pf-pending-label { font-size:13px; color:var(--db-muted); font-style:italic; }

/* Streak Card */
.pf-streak-card{
    grid-column:2;
    grid-row:1 / span 2;

    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;

    text-align:center;
    height:100%;
    min-height:0;
}
.pf-streak-main { display:flex; align-items:center; gap:4px; margin-bottom:4px; }
.pf-streak-emoji { font-size:64px; line-height:1; }
.pf-streak-number { font-size:140px; font-weight:900; line-height:1; color:var(--db-text); }
.pf-streak-label { font-size:11px; font-weight:700; letter-spacing:.12em; color:var(--db-muted); margin-bottom:4px; }
.pf-streak-best { font-size:13px; color:var(--db-muted); }

/* Middle */
.pf-middle{
    grid-column:1;
    grid-row:2;
    width:100%;
}
.pf-stats-row{
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 12px;
    height: 100%;
    width:100%;
}

.pf-stat-card{
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    padding:18px 12px;
    width:100%;
    box-sizing:border-box;
}
.pf-stat-card:hover { border-color: rgba(34, 197, 94, 0.5); transform: translateY(-2px); }
.pf-stat-label,
.pf-stat-val{
    text-align:center;
}

.pf-stat-label { font-size:16px; font-weight:500; color:var(--db-muted); }
.pf-stat-val { font-size:36px; font-weight:700; color:var(--db-text); }

.pf-stat-line {
    width:100%;
    height:1px;
    background:var(--db-border);
    margin-bottom:12px;
}

.pf-activity-card{
    display:flex;
    flex-direction:column;
    min-height:0;
    overflow:hidden;
}

.pf-activity-list{
    flex:1;
    overflow:hidden;
}
.pf-section-title { font-size:15px; font-weight:; color:var(--db-text); margin:0 0 12px 0; }
.pf-activity-item { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--db-text); padding:6px 0; }
.pf-activity-icon { flex-shrink:0; }
.pf-activity-time { margin-left:auto; font-size:11px; color:var(--db-muted); white-space:nowrap; }
.pf-empty-sm { text-align:center; color:var(--db-muted); padding:16px 0; font-size:13px; }

/* Bottom */
.pf-bottom{
    grid-column:1 / span 2;
    grid-row:3;

    display:grid;
    grid-template-columns:7.8fr 2.2fr;
    gap:16px;

    min-height:0;
}
.pf-achievements-card{
    display:flex;
    flex-direction:column;
    min-height:0;
    overflow:hidden;
}
.pf-ach-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
.pf-ach-grid{
    flex:1;

    display:grid;
    grid-template-columns:repeat(4,1fr);
    grid-template-rows:repeat(2,1fr);

    gap:12px;
}
.pf-ach-slot { aspect-ratio:1; border-radius:var(--db-radius); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; transition:all .2s; }
.pf-ach-filled { border:2px solid var(--db-accent); background:rgba(34,197,94,0.05); }
.pf-ach-filled:hover { transform:translateY(-2px); box-shadow:0 0 12px rgba(34,197,94,0.2); }
.pf-ach-empty { border:2px dashed var(--db-border); opacity:.4; }
.pf-ach-icon { width:150px; height:150px; object-fit:contain; }
.pf-ach-text { font-size:20px; }
.pf-ach-name { font-size:10px; font-weight:600; color:var(--db-text); text-align:center; max-width:90%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

@media (max-width:1024px) {
    .pf-top { grid-template-columns:1fr; }
    .pf-middle { grid-template-columns:1fr; }
    .pf-stats-row { grid-template-columns:repeat(2,1fr); }
    .pf-ach-grid { grid-template-columns:repeat(4,1fr); }
}
@media (max-width:640px) {
    .db-root { padding:16px; }
    .pf-stats-row { grid-template-columns:1fr 1fr; }
    .pf-ach-grid { grid-template-columns:repeat(3,1fr); }
}
/* --- Pagination --- */
.pagination-nav button,
.pagination-nav span {
    cursor: pointer;
}
</style>
