<div class="db-layout">
    <x-sidebar/>

    <div class="db-root">
        <header class="db-header">
            <h1 class="db-header-title" style="display: none;">Profile</h1>
        </header>

        <div class="db-profile-content">
            <!-- Profile Info Block -->
            <div class="db-card db-profile-top-card">
                <div class="db-profile-avatar-large">
                    <svg class="db-profile-avatar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="db-profile-identity">
                    <h2 class="db-profile-greeting">{{ $first_name }}</h2>
                    <p class="db-profile-handle">{{ '@' . $username }}</p>
                </div>
            </div>

            <!-- Stats Block -->
            <div class="db-stats-grid">
                <div class="db-card db-stat-card">
                    <div class="db-pstat-label">Tasks Completed</div>
                    <div class="db-pstat-line"></div>
                    <div class="db-pstat-val">{{ $tasksCompleted ?? 0 }}</div>
                </div>
                <div class="db-card db-stat-card">
                    <div class="db-pstat-label">Highest Streak</div>
                    <div class="db-pstat-line"></div>
                    <div class="db-pstat-val">{{ $highestStreak ?? 0 }}</div>
                </div>
                <div class="db-card db-stat-card">
                    <div class="db-pstat-label">Achievements Unlocked</div>
                    <div class="db-pstat-line"></div>
                    <div class="db-pstat-val">{{ $achievementsUnlocked ?? 0 }}</div>
                </div>
            </div>

            <!-- Achievements Block -->
            <div class="db-card db-achievements-card">
                <div class="db-achievements-header">
                    <h3 class="db-achievements-title">Achievements</h3>
                    <div class="db-achievements-line"></div>
                </div>
                
                <div class="db-achievements-grid">
                    @for($i = 0; $i < 6; $i++)
                        @if(isset($badges[$i]) && $badges[$i]['unlocked'])
                            <!-- Unlocked Badge -->
                            <div class="db-ach-card db-ach-unlocked">
                                @if($badges[$i]['icon'])
                                    <img src="{{ asset('AchievementBadges/' . $badges[$i]['icon']) }}" alt="{{ $badges[$i]['name'] }}" class="db-ach-icon-img">
                                @else
                                    <span class="db-ach-text">{{ $badges[$i]['name'] }}</span>
                                @endif
                            </div>
                        @else
                            <!-- Locked Placeholder -->
                            <div class="db-ach-card db-ach-locked">
                                <span class="db-ach-text">Badge</span>
                            </div>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ================================================================
   PROFILE — DARK THEME (DASHBOARD BLOCKS STYLE)
   ================================================================ */
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

.db-logo { width: 44px; height: 44px; object-fit: contain; }

.db-sidebar-middle, .db-sidebar-bottom { display: flex; flex-direction: column; gap: 32px; }

.db-nav-item {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: var(--db-muted);
    transition: all 0.2s;
    background: transparent; border: none; cursor: pointer;
}
.db-nav-item:hover { color: var(--db-text); background: var(--db-surface); }
.db-nav-item.active {
    color: var(--db-accent);
    background: rgba(34, 197, 94, 0.1);
    box-shadow: 0 0 15px rgba(34, 197, 94, 0.2);
}

.db-root {
    flex: 1; display: flex; flex-direction: column;
    background: var(--db-bg); color: var(--db-text);
    padding: 32px 40px; box-sizing: border-box; overflow-y: auto;
}

.db-header { margin-bottom: 0px; flex-shrink: 0; }
.db-header-title { font-size: 28px; font-weight: 700; margin: 0; }

/* ── SHARED CARD STYLES ── */
.db-card {
    background: var(--db-surface);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 32px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* ── PROFILE CONTENT ── */
.db-profile-content {
    max-width: 900px;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-top: 20px;
}

/* TOP BLOCK */
.db-profile-top-card {
    display: flex;
    align-items: center;
    gap: 32px;
}

.db-profile-avatar-large {
    width: 120px;
    height: 120px;
    background: var(--db-surface2);
    border-radius: 50%;
    border: 3px solid var(--db-accent); /* Blue border from design */
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
}

.db-profile-avatar-icon { width: 60px; height: 60px; color: var(--db-muted); }

.db-profile-identity { display: flex; flex-direction: column; justify-content: center; }

.db-profile-greeting {
    font-size: 36px;
    font-weight: 700;
    color: var(--db-text);
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
}

.db-profile-handle {
    font-size: 18px;
    font-weight: 500;
    color: var(--db-muted);
    margin: 0;
}

/* STATS BLOCK */
.db-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.db-stat-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px;
    transition: transform 0.2s, border-color 0.2s;
}

.db-stat-card:hover {
    border-color: #3a3d50;
    transform: translateY(-2px);
}

.db-pstat-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--db-text);
    margin-bottom: 12px;
    text-align: center;
}

.db-pstat-line {
    width: 100%;
    height: 1px;
    background: var(--db-border);
    margin-bottom: 16px;
}

.db-pstat-val {
    font-size: 42px;
    font-weight: 700;
    color: var(--db-text);
    line-height: 1;
}

/* ACHIEVEMENTS BLOCK */
.db-achievements-header {
    display: flex;
    align-items: center;
    margin-bottom: 24px;
}

.db-achievements-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--db-text);
    margin: 0;
    white-space: nowrap;
}

.db-achievements-line {
    flex: 1;
    height: 1px;
    background: var(--db-border);
    margin-left: 20px;
}

.db-achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 20px;
}

.db-ach-card {
    aspect-ratio: 1;
    border-radius: var(--db-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--db-surface2);
    transition: all 0.2s;
}

/* Design shows bright green borders for badges */
.db-ach-unlocked {
    border: 2px solid var(--db-accent);
    background: rgba(34, 197, 94, 0.05);
}

.db-ach-locked {
    border: 2px solid var(--db-border); /* Placeholder uses a darker border */
    opacity: 0.6;
}

.db-ach-text {
    font-size: 14px;
    font-weight: 600;
    color: var(--db-text);
}

.db-ach-icon-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 16px;
}

@media (max-width: 768px) {
    .db-profile-top-card { flex-direction: column; align-items: center; text-align: center; gap: 24px; }
    .db-stats-grid { grid-template-columns: 1fr; gap: 16px; }
}

@media (max-width: 640px) {
    .db-root { padding: 16px; }
    .db-achievements-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
