<div class="db-layout">
    <x-sidebar />

    <div class="db-root">
        <div class="db-top-fixed">
            <header class="db-header">
                <h1 class="db-header-title">Achievements</h1>
            </header>
    
            <div class="db-stats">
                <div class="db-stats-profile-card">
                    {{-- Avatar --}}
                    <div class="db-stats-avatar-wrapper">
                        {{-- TODO: swap src with $avatarUrl once API returns it --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24"
                             fill="none" stroke="#00ff88" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
        
                    {{-- Name + username + progress --}}
                    <div class="db-stats-profile-info">
                        <span class="db-stats-name">
                            {{ $first_name }}
                        </span>
                        <span class="db-stats-username">{{ $username }}</span>
        
                        {{-- XP progress bar --}}
                        <div class="db-stats-progress-wrapper">
                            <div class="db-stats-progress-bg">
                                <div class="db-stats-progress-fill" style="width:{{ $completionPercentage }}%;"></div>
                            </div>
                            <span class="db-stats-progress-label">
                                {{ $completionPercentage }}% complete
                            </span>
                        </div>
                    </div>
                </div>
        
                {{-- Total Achievements card --}}
                <div class="db-stats-card db-stats-total-card">
                    <span class="db-stats-card-label">Total</span>
                    <span class="db-stats-card-value">{{ $totalAchievements }}</span>
                    <span class="db-stats-card-subtext">Achievements</span>
                </div>
        
                {{-- Unlocked card (green neon accent) --}}
                <div class="db-stats-card db-stats-unlocked-card">
                    <span class="db-stats-unlocked-label">Unlocked</span>
                    <span class="db-stats-unlocked-value">{{ $unlockedCount }}</span>
                    <span class="db-stats-unlocked-subtext">Badges earned</span>
                </div>
            </div>
        </div>
    
        <div class="db-main">
            <div class="db-showcase-container">
                <div class="db-showcase-wrapper">
                    @foreach ($categories as $category)
                        <div>
                            {{-- Category header + divider --}}
                            <div class="db-category-header">
                                <h3 class="db-category-title">{{ $category['label'] }}</h3>
                                <div class="db-category-divider"></div>
                                <span class="db-category-count">{{ $category['unlockedCount'] }}/{{ $category['total'] }}</span>
                            </div>
        
                            {{-- Badge grid --}}
                            <div class="db-badge-grid">
                                @foreach ($category['badges'] as $badge)
                                    {{-- Badge card --}}
                                    <div class="db-badge-card {{ $badge['unlocked'] ? 'db-badge-unlocked' : 'db-badge-locked' }}">
        
                                        {{-- Icon --}}
                                        <span class="db-badge-icon">
                                            @if(str_ends_with($badge['icon'], '.png') || str_ends_with($badge['icon'], '.jpg') || str_ends_with($badge['icon'], '.svg'))
                                                <img src="{{ asset('AchievementBadges/' . ltrim($badge['icon'], '/')) }}" alt="{{ $badge['name'] }}" style="width:2.2rem;height:2.2rem;object-fit:contain;">
                                            @else
                                                {{ $badge['icon'] ?: '🏆' }}
                                            @endif
                                        </span>
        
                                        {{-- Lock icon for locked badges --}}
                                        @unless ($badge['unlocked'])
                                            <div class="db-badge-lock">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                                </svg>
                                            </div>
                                        @endunless
        
                                        {{-- Tooltip (CSS-only) --}}
                                        <div class="db-badge-tooltip">
                                            <div class="db-badge-tooltip-content">
                                                <p class="db-badge-tooltip-name">{{ $badge['name'] }}</p>
                                                <p class="db-badge-tooltip-desc">{{ $badge['description'] }}</p>
                                            </div>
                                            <div class="db-badge-tooltip-arrow"></div>
                                        </div>
        
                                    </div>{{-- /badge card --}}
                                @endforeach
                            </div>{{-- /grid --}}
                        </div>
                    @endforeach
                </div>
            </div>{{-- /showcase --}}
        </div>
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
    width: 44px;
    height: 44px;
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
    background: rgba(34, 197, 94, 0.1);
    box-shadow: 0 0 15px rgba(34, 197, 94, 0.2);
}

.db-root {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
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
    padding-bottom: 32px;
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

.db-stats {
    display: flex;
    gap: 16px;
    align-items: stretch;
}

/* Profile Card */
.db-stats-profile-card {
    flex: 3;
    display: flex;
    align-items: center;
    gap: 24px; 
    padding: 24px; 
    border-radius: 12px; 
    background: #15161b;
    border: 1px solid rgba(255,255,255,0.05);
}

.db-stats-avatar-wrapper {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%; 
    width: 72px;
    height: 72px;
    background: #0f1c16;
    border: 1px solid #165636;
}
.db-stats-avatar-wrapper svg {
    stroke: #10b981;
}

.db-stats-profile-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.db-stats-name {
    color: #ffffff; 
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis; 
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 4px;
}

.db-stats-username {
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 12px;
}

.db-stats-progress-wrapper {
    display: flex;
    align-items: center;
    gap: 12px; 
}

.db-stats-progress-bg {
    flex: 1;
    max-width: 140px;
    border-radius: 9999px; 
    height: 4px;
    background: #2a2d3e;
}

.db-stats-progress-fill {
    height: 100%;
    border-radius: 9999px; 
    background: #10b981;
}

.db-stats-progress-label {
    color: #64748b;
    font-size: 0.75rem;
}

/* Common Card Styles */
.db-stats-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px; 
    border-radius: 12px; 
}

/* Total Card */
.db-stats-total-card {
    flex: 1.5;
    background: #15161b;
    border: 1px solid rgba(255,255,255,0.05);
}

.db-stats-card-label {
    color: #64748b;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.db-stats-card-value {
    color: #ffffff;
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.1;
    margin: 8px 0;
}

.db-stats-card-subtext {
    color: #64748b;
    font-size: 0.75rem;
}

/* Unlocked Card */
.db-stats-unlocked-card {
    flex: 1.5;
    background: #101c15;
    border: 1px solid #16462d;
}

.db-stats-unlocked-label {
    color: #10b981;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.db-stats-unlocked-value {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.1;
    color: #10b981;
    text-shadow: 0 0 16px rgba(16,185,129,0.4);
    margin: 8px 0;
}

.db-stats-unlocked-subtext {
    color: #06744e;
    font-size: 0.75rem;
}

/* Showcase Container */
.db-showcase-container {
    padding-bottom: 40px;
    padding: 0 5px;
}

.db-showcase-wrapper {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

/* Category Header */
.db-category-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
}

.db-category-title {
    color: #e2e8f0;
    font-size: 0.95rem;
    font-weight: 600;
    white-space: nowrap;
    margin: 0;
}

.db-category-divider {
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.06);
}

.db-category-count {
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Badge Grid */
.db-badge-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.75rem;
}

@media (max-width: 1200px) { .db-badge-grid { grid-template-columns: repeat(5, 1fr); } }
@media (max-width: 900px)  { .db-badge-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 600px)  { .db-badge-grid { grid-template-columns: repeat(3, 1fr); } }

/* Badge Card */
.db-badge-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    cursor: pointer;
    aspect-ratio: 1/1;
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
    z-index: 10;
}

.db-badge-locked {
    background: #121317;
    border: 1px solid rgba(255,255,255,0.02);
}

.db-badge-icon {
    font-size: 2rem;
    line-height: 1;
    user-select: none;
    transition: filter 0.2s;
}

.db-badge-locked .db-badge-icon {
    filter: grayscale(1) brightness(0.25);
    opacity: 0.5;
}

.db-badge-lock {
    position: absolute;
    bottom: 8px;
    right: 8px;
}
.db-badge-lock svg {
    width: 12px;
    height: 12px;
    stroke: #333;
}

/* Tooltip */
.db-badge-tooltip {
    position: absolute;
    pointer-events: none;
    z-index: 999;
    opacity: 0;
    bottom: calc(100% + 0.5rem);
    left: 50%;
    transform: translateX(-50%);
    transition: opacity 0.15s ease;
    width: 10rem;
}

.db-badge-card:hover .db-badge-tooltip {
    opacity: 1;
}

.db-badge-tooltip-content {
    border-radius: 12px;
    padding: 8px 12px;
    text-align: center;
    background: #1e1e1e;
    border: 1px solid #2a2a2a;
    box-shadow: 0 8px 24px rgba(0,0,0,.5);
}

.db-badge-tooltip-name {
    color: var(--db-text);
    font-size: 0.75rem;
    margin: 0 0 0.2rem 0;
    font-weight: 500;
}

.db-badge-tooltip-desc {
    font-size: 0.625rem;
    color: #555555;
    line-height: 1.4;
    margin: 0;
}

.db-badge-tooltip-rarity {
    font-size: 0.625rem;
    margin: 0.3rem 0 0 0;
}

.db-badge-tooltip-arrow {
    width: 0.5rem;
    height: 0.5rem;
    background: #1e1e1e;
    border-bottom: 1px solid #2a2a2a;
    border-right: 1px solid #2a2a2a;
    transform: rotate(45deg);
    margin: -0.26rem auto 0;
}

@media (max-width: 900px) {
    .db-stats {
        flex-direction: column;
    }
}
</style>