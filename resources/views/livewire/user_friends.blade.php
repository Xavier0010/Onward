<div class="db-layout">
    <x-sidebar active="friends"/>

    <div class="db-root">
        {{-- HEADER --}}
        <header class="fr-header">
            <h1 class="db-header-title">Friends</h1>
        </header>

        {{-- SEARCH + ACTIONS BAR --}}
        <div class="fr-toolbar">
            <div class="db-search-wrap" style="flex:1; max-width:400px;">
                <svg class="db-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input
                    type="text"
                    wire:model.live="searchQuery"
                    placeholder="Search friends..."
                    class="db-search"
                />
            </div>
            <div class="fr-actions">
                <button wire:click="openAddFriendModal" class="fr-btn fr-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    Add Friend
                </button>
                <button wire:click="openPendingModal" class="fr-btn fr-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Pending
                    @if($pendingCount > 0)
                        <span class="fr-badge-count">{{ $pendingCount }}</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- FRIEND LIST --}}
        <div class="fr-list-container">
            @forelse($friends as $friend)
                @php
                    $initials = strtoupper(substr($friend['first_name'] ?? '', 0, 1) . substr($friend['last_name'] ?? '', 0, 1));
                    $friendshipRecord = \App\Models\Friendship::where('status', 'accepted')
                        ->where(function($q) use ($friend) {
                            $q->where(function($q2) use ($friend) {
                                $q2->where('sender_id', auth()->id())->where('receiver_id', $friend['id']);
                            })->orWhere(function($q2) use ($friend) {
                                $q2->where('sender_id', $friend['id'])->where('receiver_id', auth()->id());
                            });
                        })->first();
                @endphp
                <a href="/user/profile/{{ $friend['id'] }}" class="fr-friend-row" wire:key="friend-{{ $friend['id'] }}">
                    <div class="fr-friend-left">
                        @if(!empty($friend['avatar']))
                            <img src="{{ Storage::url($friend['avatar']) }}" class="fr-avatar" alt="{{ $friend['first_name'] }}">
                        @else
                            <div class="fr-avatar fr-avatar-initials">{{ $initials }}</div>
                        @endif
                        <div class="fr-friend-info">
                            <span class="fr-friend-name">{{ $friend['full_name'] ?? $friend['first_name'] . ' ' . $friend['last_name'] }}</span>
                            <span class="fr-friend-username">{{ '@' . $friend['username'] }}</span>
                        </div>
                    </div>
                    <div class="fr-friend-right">
                        <span class="fr-streak-badge">🔥 {{ $friend['streak_count'] ?? 0 }}</span>
                        @if($friendshipRecord)
                            <button
                                wire:click.prevent.stop="openConfirmModal('removeFriend', 'Remove Friend?', 'Are you sure you want to remove this friend?', {{ $friendshipRecord->id }})"
                                class="fr-remove-btn"
                                title="Remove Friend"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="18" y1="11" x2="23" y2="11"/></svg>
                            </button>
                        @endif
                    </div>
                </a>
            @empty
                <div class="fr-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--db-muted); margin-bottom:12px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    <p>No friends yet. Add some friends to get started!</p>
                </div>
            @endforelse
        </div>

        {{-- ═══════ ADD FRIEND MODAL ═══════ --}}
        @if($showAddFriendModal)
        <div wire:key="modal-add-friend" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="closeAddFriendModal">
            <div class="w-full max-w-lg bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">Add Friend</h2>
                    <button type="button" wire:click="closeAddFriendModal" class="text-gray-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <div class="db-search-wrap mb-4">
                    <svg class="db-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="addFriendSearch"
                        placeholder="Search by username or name..."
                        class="db-search"
                    />
                </div>

                <div class="fr-search-results">
                    @forelse($searchResults as $user)
                        @php
                            $userInitials = strtoupper(substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1));
                        @endphp
                        <div class="fr-search-row" wire:key="search-{{ $user['id'] }}">
                            <div class="fr-friend-left">
                                @if(!empty($user['avatar']))
                                    <img src="{{ Storage::url($user['avatar']) }}" class="fr-avatar fr-avatar-sm" alt="">
                                @else
                                    <div class="fr-avatar fr-avatar-sm fr-avatar-initials">{{ $userInitials }}</div>
                                @endif
                                <div class="fr-friend-info">
                                    <span class="fr-friend-name">{{ $user['full_name'] ?? $user['first_name'] . ' ' . $user['last_name'] }}</span>
                                    <span class="fr-friend-username">{{ '@' . $user['username'] }}</span>
                                </div>
                            </div>
                            <button wire:click="sendRequest({{ $user['id'] }})" class="fr-btn fr-btn-primary fr-btn-sm">
                                Add
                            </button>
                        </div>
                    @empty
                        @if(strlen($addFriendSearch) >= 2)
                            <div class="fr-empty-sm">No users found.</div>
                        @else
                            <div class="fr-empty-sm">Type at least 2 characters to search.</div>
                        @endif
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ═══════ PENDING REQUESTS MODAL ═══════ --}}
        @if($showPendingModal)
        <div wire:key="modal-pending" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="closePendingModal">
            <div class="w-full max-w-lg bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">Pending Requests</h2>
                    <button type="button" wire:click="closePendingModal" class="text-gray-400 hover:text-white cursor-pointer">✕</button>
                </div>

                {{-- Incoming --}}
                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-3 border-b border-[#2a2d3e] pb-2">Incoming Requests</h3>
                <div class="fr-search-results mb-6">
                    @forelse($incomingRequests as $req)
                        @php
                            $ri = strtoupper(substr($req['user']['first_name'] ?? '', 0, 1) . substr($req['user']['last_name'] ?? '', 0, 1));
                        @endphp
                        <div class="fr-search-row" wire:key="incoming-{{ $req['id'] }}">
                            <div class="fr-friend-left">
                                <div class="fr-avatar fr-avatar-sm fr-avatar-initials">{{ $ri }}</div>
                                <div class="fr-friend-info">
                                    <span class="fr-friend-name">{{ $req['user']['full_name'] }}</span>
                                    <span class="fr-friend-username">{{ '@' . $req['user']['username'] }}</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="acceptRequest({{ $req['id'] }})" class="fr-btn fr-btn-primary fr-btn-sm">Accept</button>
                                <button wire:click="rejectRequest({{ $req['id'] }})" class="fr-btn fr-btn-danger fr-btn-sm">Reject</button>
                            </div>
                        </div>
                    @empty
                        <div class="fr-empty-sm">No incoming requests.</div>
                    @endforelse
                </div>

                {{-- Outgoing --}}
                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-3 border-b border-[#2a2d3e] pb-2">Outgoing Requests</h3>
                <div class="fr-search-results">
                    @forelse($outgoingRequests as $req)
                        @php
                            $ro = strtoupper(substr($req['user']['first_name'] ?? '', 0, 1) . substr($req['user']['last_name'] ?? '', 0, 1));
                        @endphp
                        <div class="fr-search-row" wire:key="outgoing-{{ $req['id'] }}">
                            <div class="fr-friend-left">
                                <div class="fr-avatar fr-avatar-sm fr-avatar-initials">{{ $ro }}</div>
                                <div class="fr-friend-info">
                                    <span class="fr-friend-name">{{ $req['user']['full_name'] }}</span>
                                    <span class="fr-friend-username">{{ '@' . $req['user']['username'] }}</span>
                                </div>
                            </div>
                            <button wire:click="cancelRequest({{ $req['id'] }})" class="fr-btn fr-btn-secondary fr-btn-sm">Cancel</button>
                        </div>
                    @empty
                        <div class="fr-empty-sm">No outgoing requests.</div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="$set('showConfirmModal', false)">
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
    </div>
</div>

<style>
:root {
    --db-bg:          #0f1117;
    --db-surface:     #1a1d27;
    --db-surface2:    #1f2235;
    --db-border:      #2a2d3e;
    --db-text:        #e2e8f0;
    --db-muted:       #64748b;
    --db-accent:      #22c55e;
    --db-accent2:     #3b82f6;
    --db-radius:      12px;
    --db-radius-sm:   8px;
}

.db-layout { display:flex; height:100vh; background:var(--db-bg); overflow:hidden; font-family:'Inter','Segoe UI',system-ui,sans-serif; }

.db-sidebar-nav { width:80px; background:#0f1117; border-right:1px solid var(--db-border); display:flex; flex-direction:column; align-items:center; padding:32px 0; justify-content:space-between; flex-shrink:0; }
.db-logo { width:50px; height:50px; object-fit:contain; }
.db-sidebar-middle,.db-sidebar-bottom { display:flex; flex-direction:column; gap:32px; }
.db-nav-item { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--db-muted); transition:all .2s; }
.db-nav-item:hover { color:var(--db-text); background:var(--db-surface); }
.db-nav-item.active { color:var(--db-accent); background:rgba(34,197,94,0.1); box-shadow:0 0 15px rgba(34,197,94,0.2); }

.db-root { flex:1; display:flex; flex-direction:column; overflow:hidden; background:var(--db-bg); color:var(--db-text); padding:32px 40px; box-sizing:border-box; }
.db-header-title { font-size:28px; font-weight:700; color:var(--db-text); margin:0; }

.db-search-wrap { flex:1; position:relative; min-width:200px; }
.db-search-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--db-muted); }
.db-search { width:100%; background:var(--db-surface2); border:1px solid var(--db-border); border-radius:var(--db-radius-sm); padding:9px 12px 9px 36px; color:var(--db-text); font-size:14px; outline:none; box-sizing:border-box; transition:border-color .2s; }
.db-search::placeholder { color:var(--db-muted); }
.db-search:focus { border-color:var(--db-accent2); }

/* ── FRIENDS PAGE ── */
.fr-header { margin-bottom:20px; flex-shrink:0; }

.fr-toolbar {
    display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-shrink:0;
}

.fr-actions { display:flex; gap:8px; }

.fr-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 16px; border-radius:8px; border:none;
    font-size:14px; font-weight:500; cursor:pointer;
    transition:all .2s; line-height:1;
}
.fr-btn-primary { background:var(--db-accent); color:#fff; }
.fr-btn-primary:hover { background:#1ea951; }
.fr-btn-secondary { background:var(--db-surface2); color:var(--db-text); border:1px solid var(--db-border); }
.fr-btn-secondary:hover { background:var(--db-border); }
.fr-btn-danger { background:#ef444430; color:#ef4444; border:1px solid #ef444440; }
.fr-btn-danger:hover { background:#ef444450; }
.fr-btn-sm { padding:6px 12px; font-size:12px; }

.fr-badge-count {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:20px; height:20px; border-radius:10px;
    background:#ef4444; color:#fff; font-size:11px; font-weight:700;
    padding:0 5px; margin-left:4px;
}

/* ── FRIEND LIST ── */
.fr-list-container {
    flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:4px;
    padding-right:4px;
}

.fr-friend-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 18px; border-radius:var(--db-radius-sm);
    background:var(--db-surface); border:1px solid var(--db-border);
    text-decoration:none; color:inherit;
    transition:background .15s, border-color .15s, transform .15s;
    cursor:pointer;
}
.fr-friend-row:hover {
    background:var(--db-surface2); border-color:rgba(34,197,94,0.3);
    transform:translateX(4px);
}

.fr-friend-left { display:flex; align-items:center; gap:14px; }
.fr-friend-right { display:flex; align-items:center; gap:12px; }

.fr-avatar {
    width:40px; height:40px; border-radius:50%;
    object-fit:cover; flex-shrink:0;
}
.fr-avatar-sm { width:34px; height:34px; }
.fr-avatar-initials {
    display:flex; align-items:center; justify-content:center;
    background:var(--db-accent)20; color:var(--db-accent);
    border:1.5px solid var(--db-accent)40;
    font-size:13px; font-weight:700;
}

.fr-friend-info { display:flex; flex-direction:column; }
.fr-friend-name { font-size:14px; font-weight:600; color:var(--db-text); }
.fr-friend-username { font-size:12px; color:var(--db-muted); }

.fr-streak-badge {
    font-size:13px; font-weight:600; color:#f59e0b;
    background:#f59e0b15; padding:4px 10px; border-radius:20px;
}

.fr-remove-btn {
    width:32px; height:32px; border-radius:8px; border:none;
    background:transparent; color:#ef4444; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    transition:background .18s;
    opacity:0;
}
.fr-friend-row:hover .fr-remove-btn { opacity:1; }
.fr-remove-btn:hover { background:rgba(239,68,68,0.12); }

.fr-empty {
    text-align:center; color:var(--db-muted); padding:60px 0;
    display:flex; flex-direction:column; align-items:center;
    font-size:14px;
}
.fr-empty-sm { text-align:center; color:var(--db-muted); padding:20px 0; font-size:13px; }

/* ── SEARCH RESULTS (modals) ── */
.fr-search-results { display:flex; flex-direction:column; gap:4px; max-height:300px; overflow-y:auto; }
.fr-search-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px; border-radius:var(--db-radius-sm);
    background:var(--db-surface2); transition:background .15s;
}
.fr-search-row:hover { background:#242740; }

@media (max-width:768px) {
    .fr-toolbar { flex-direction:column; align-items:stretch; }
    .fr-actions { justify-content:stretch; }
    .fr-btn { flex:1; justify-content:center; }
}
</style>
