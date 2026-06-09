
<div class="db-layout">
    <x-sidebar active="store"/>

    <div class="db-root">
        <div class="db-top-fixed">
            <header class="db-header flex justify-between items-center">
                <h1 class="db-header-title">Store</h1>
                <div class="flex items-center gap-2 bg-[#15161b] border border-[rgba(255,255,255,0.05)] px-4 py-2 rounded-xl text-[#22c55e] font-bold">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                    </svg>
                    <span>{{ $availableXp }} XP</span>
                </div>
            </header>
        </div>

        <div class="db-main">
            <div class="db-showcase-container">
                <div class="db-showcase-wrapper">
                    @if ($borders)
                        @foreach ($borders as $rarity => $items)
                            <div class="db-category-container">
                                <div class="db-category-header" style="width: 100%;">
                                    <h3 class="db-category-title capitalize">{{ $rarity }}</h3>
                                    <div class="db-category-divider"></div>
                                </div>

                                <div class="grid grid-cols-4 gap-4 mt-4">
                                    @foreach ($items as $item)
                                        @php
                                            $owned = in_array($item['id'], $ownedBorderIds);
                                            $active = $owned && $item['id'] == $activeBorderId;
                                        @endphp
                                        <div class="db-badge-card {{ $owned ? 'db-badge-unlocked' : 'db-badge-locked' }}" style="aspect-ratio: auto; padding: 1rem; flex-direction: column; gap: 1rem;">
                                            <div class="w-24 h-24 rounded-full flex items-center justify-center" style="border: 4px solid {{ str_starts_with($item['color'], 'linear-gradient') ? '#f59e0b' : $item['color'] }}; background: {{ $item['color'] }}; opacity: 0.3;">
                                                <div class="w-16 h-16 rounded-full bg-[#1a1d27]"></div>
                                            </div>

                                            <div class="text-center">
                                                <p class="db-badge-tooltip-name text-sm">{{ $item['name'] }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $item['price'] }} XP</p>
                                            </div>

                                            @if ($owned)
                                                @if ($active)
                                                    <button disabled class="px-4 py-2 bg-[#22c55e] text-white text-sm rounded-lg font-semibold cursor-not-allowed cursor-pointer">
                                                        Equipped
                                                    </button>
                                                @else
                                                    <button wire:click="setActiveBorder({{ $item['id'] }})" class="px-4 py-2 bg-[#1a1d27] border border-[#22c55e] text-[#22c55e] text-sm rounded-lg font-semibold hover:bg-[#22c55e] hover:text-white transition-colors cursor-pointer">
                                                        Equip
                                                    </button>
                                                @endif
                                            @else
                                                <button wire:click="buyBorder({{ $item['id'] }})" class="px-4 py-2 bg-[#22c55e] text-white text-sm rounded-lg font-semibold hover:bg-[#1ea951] transition-colors cursor-pointer">
                                                    Buy
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ NOTIFICATION MODAL ═══ --}}
    @if($showNotificationModal)
    <div wire:key="modal-notification" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="$set('showNotificationModal', false)">
        <div class="w-full max-w-sm bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold {{ $notificationType === 'success' ? 'text-[#22c55e]' : ($notificationType === 'warning' ? 'text-[#f59e0b]' : 'text-[#ef4444]') }}">{{ $notificationTitle }}</h2>
                <button type="button" wire:click="$set('showNotificationModal', false)" class="text-gray-400 hover:text-white cursor-pointer">✕</button>
            </div>
            <p class="text-gray-400 mb-6">{{ $notificationMessage }}</p>
            <div class="flex justify-end">
                <button wire:click="$set('showNotificationModal', false)" class="px-4 py-2 rounded-lg bg-[#22c55e] text-white cursor-pointer hover:bg-[#1ea951]">Got it!</button>
            </div>
        </div>
    </div>
    @endif
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
    --db-high:        #ef4444;
    --db-medium:      #f59e0b;
    --db-low:         #6366f1;
    --db-radius:      12px;
    --db-radius-sm:   8px;
}

.db-layout {
    display: flex;
    height: 100vh;
    background: var(--db-bg);
    overflow: hidden;
    font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
}

.db-sidebar-nav {
    width: 80px;
    background: #0f1117;
    border-right: 1px solid var(--db-border);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32px 0;
    justify-content: space-between;
    flex-shrink: 0;
}

.db-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
}

.db-sidebar-middle, .db-sidebar-bottom {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.db-nav-item {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--db-muted);
    transition: all 0.2s;
}

.db-nav-item:hover {
    color: var(--db-text);
    background: var(--db-surface);
}

.db-nav-item.active {
    color: var(--db-accent);
    background: rgba(34,197,94,0.1);
    box-shadow: 0 0 15px rgba(34,197,94,0.2);
}

.db-root {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: visible;
    background: var(--db-bg);
    color: var(--db-text);
    padding: 0 40px;
    box-sizing: border-box;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.db-root::-webkit-scrollbar {
    display: none;
}

.db-top-fixed {
    position: sticky;
    top: 0;
    background: var(--db-bg);
    z-index: 50;
    padding-top: 32px;
    padding-bottom: 16px;
}

.db-header {
    margin-bottom: 24px;
    flex-shrink: 0;
}

.db-header-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--db-text);
    margin: 0;
}

.db-main {
    width: 100%;
    overflow: visible;
    box-sizing: border-box;
}

.db-category-container {
    width: 100%;
    box-sizing: border-box;
}

.db-category-header {
    display: flex;
    align-items: center;
    gap: 16px;
    width: 100%;
    box-sizing: border-box;
    margin: 12px 0;
}

.db-category-title {
    color: #e2e8f0;
    font-size: 0.95rem;
    font-weight: 500;
    white-space: nowrap;
    margin: 0;
    flex-shrink: 0;
}

.db-category-divider {
    height: 1px;
    flex: 1;
    background: rgba(255,255,255,0.06);
    flex-grow: 1;
}

.db-badge-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.db-badge-unlocked {
    background: #18191e;
    border: 1px solid rgba(255,255,255,0.05);
}

.db-badge-unlocked:hover {
    border: 1px solid #22c55e;
    box-shadow: 0 0 0 1px #22c55e, 0 4px 20px rgba(34,197,94,0.15);
    transform: translateY(-2px);
}

.db-badge-locked {
    background: #121317;
    border: 1px solid rgba(255,255,255,0.02);
}

.db-showcase-container {
    padding-bottom: 40px;
    padding: 0 5px;
    width: 100%;
    box-sizing: border-box;
}

.db-showcase-wrapper {
    display: flex;
    flex-direction: column;
    gap: 32px;
    width: 100%;
}
</style>
