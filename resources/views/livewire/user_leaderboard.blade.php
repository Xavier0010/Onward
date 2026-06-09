<div class="db-layout">
    <x-sidebar active="leaderboard" />

    <div class="db-root">
        {{-- HEADER --}}
        <header class="fr-header">
            <h1 class="db-header-title">Leaderboard</h1>
        </header>

        {{-- PODIUM SECTION --}}
        <div class="flex justify-center items-end gap-6 mb-8 mt-22 h-[300px]">
            @php
                $podiumOrder = [
                    1 => ['height' => 'h-[160px]', 'bg' => 'bg-[#1a1d27]', 'medal' => '🥈', 'text' => 'text-gray-400', 'border' => 'border-gray-400'],
                    0 => ['height' => 'h-[200px]', 'bg' => 'bg-[#22c55e]/10 border border-[#22c55e]/30', 'medal' => '🥇', 'text' => 'text-yellow-500', 'border' => 'border-yellow-500'],
                    2 => ['height' => 'h-[140px]', 'bg' => 'bg-[#1a1d27]', 'medal' => '🥉', 'text' => 'text-amber-700', 'border' => 'border-amber-700'],
                ];
                
                $podiumIndexes = [1, 0, 2]; // 2nd, 1st, 3rd display order
            @endphp

            @foreach($podiumIndexes as $index)
                @if(isset($podium[$index]))
                    @php 
                        $user = $podium[$index]; 
                        $style = $podiumOrder[$index];
                        $initials = strtoupper(substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1));
                    @endphp
                    <a href="/user/profile/{{ $user['id'] }}" class="flex flex-col items-center w-[140px] group">
                        <div class="text-3xl mb-2">{{ $style['medal'] }}</div>
                        <div class="relative mb-9">
                            @if(!empty($user['avatar']))
                                <img src="{{ Storage::url($user['avatar']) }}" class="w-20 h-20 rounded-full object-cover border-4 {{ $style['border'] }}">
                            @else
                                <div class="w-20 h-20 rounded-full bg-[#1f2235] border-4 {{ $style['border'] }} flex items-center justify-center text-xl font-bold text-gray-300">{{ $initials }}</div>
                            @endif
                            <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-[#1a1d27] border-2 border-[#2a2d3e] flex items-center justify-center text-sm font-bold {{ $style['text'] }}">
                                {{ $index + 1 }}
                            </div>
                        </div>
                        <div class="{{ $style['height'] }} w-full {{ $style['bg'] }} rounded-t-xl flex flex-col items-center pt-6 px-2 text-center transition-all hover:-translate-y-2">
                            <span class="font-bold text-white text-sm line-clamp-1 w-full">{{ $user['first_name'] }} {{ $user['last_name'] }}</span>
                            <span class="text-xs text-gray-500 font-semibold mt-1">
                                {{ $user['value'] }} {{ $type === 'xp' ? 'XP' : 'Days' }}
                            </span>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
        
        <div class="flex justify-center mb-6">
            <div class="inline-flex bg-[#1a1d27] border border-[#2a2d3e] rounded-xl p-1">
                <button wire:click="setType('xp')" class="cursor-pointer px-6 py-2 rounded-lg text-sm font-semibold transition-all {{ $type === 'xp' ? 'bg-[#22c55e] text-white' : 'text-gray-400 hover:text-white' }}">XP</button>
                <button wire:click="setType('streak')" class="cursor-pointer px-6 py-2 rounded-lg text-sm font-semibold transition-all {{ $type === 'streak' ? 'bg-[#22c55e] text-white' : 'text-gray-400 hover:text-white' }}">Streak</button>
            </div>
        </div>

        {{-- RANKINGS LIST --}}
        <div class="max-w-3xl mx-auto w-full flex flex-col gap-3">
            @if(count($rankings) > 0)
                <div class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2 px-4">Other Ranks</div>
                @foreach($rankings as $user)
                    @php
                        $initials = strtoupper(substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1));
                    @endphp
                    <a href="/user/profile/{{ $user['id'] }}" class="flex items-center justify-between p-4 rounded-xl bg-[#1a1d27] border border-[#2a2d3e] hover:bg-[#1f2235] hover:border-[#3a3d50] transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-8 text-center font-bold text-gray-500">#{{ $user['rank'] }}</div>
                            @if(!empty($user['avatar']))
                                <img src="{{ Storage::url($user['avatar']) }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-[#1f2235] border border-[#2a2d3e] flex items-center justify-center text-sm font-bold text-gray-400">{{ $initials }}</div>
                            @endif
                            <div class="flex flex-col">
                                <span class="font-semibold text-white group-hover:text-[#22c55e] transition-colors">{{ $user['first_name'] }} {{ $user['last_name'] }}</span>
                                <span class="text-xs text-gray-500">{{ '@' . $user['username'] }}</span>
                            </div>
                        </div>
                        <div class="font-bold {{ $type === 'xp' ? 'text-[#22c55e]' : 'text-[#f59e0b]' }}">
                            {{ $user['value'] }} {{ $type === 'xp' ? 'XP' : 'Days' }}
                        </div>
                    </a>
                @endforeach
            @else
                <div class="text-center py-10 text-gray-500 bg-[#1a1d27] rounded-xl border border-[#2a2d3e]">
                    No other users to display.
                </div>
            @endif
        </div>
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

.db-root { flex:1; display:flex; flex-direction:column; overflow:hidden; background:var(--db-bg); color:var(--db-text); padding:32px 40px; box-sizing:border-box; }
.db-header-title { font-size:28px; font-weight:700; color:var(--db-text); margin:0; }

.db-sidebar-nav { width:80px; background:#0f1117; border-right:1px solid var(--db-border); display:flex; flex-direction:column; align-items:center; padding:32px 0; justify-content:space-between; flex-shrink:0; }
.db-logo { width:50px; height:50px; object-fit:contain; }
.db-sidebar-middle,.db-sidebar-bottom { display:flex; flex-direction:column; gap:32px; }
.db-nav-item { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--db-muted); transition:all .2s; }
.db-nav-item:hover { color:var(--db-text); background:var(--db-surface); }
.db-nav-item.active { color:var(--db-accent); background:rgba(34,197,94,0.1); box-shadow:0 0 15px rgba(34,197,94,0.2); }

/* Custom Scrollbar */
::-webkit-scrollbar { width:8px; height:8px; }
::-webkit-scrollbar-track { background:var(--db-bg); }
::-webkit-scrollbar-thumb { background:var(--db-surface2); border-radius:4px; }
::-webkit-scrollbar-thumb:hover { background:var(--db-muted); }
</style>
