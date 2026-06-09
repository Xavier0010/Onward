<div class="db-layout">
    <x-sidebar active="dashboard"/>

    <div class="db-root">
        <header class="db-header flex justify-between items-center w-full mb-[20px]">
            <h1 class="db-header-title">App Statistics</h1>
        </header>

        <div class="db-stats">
            <div class="db-stat-card">
                <div class="db-stat-info">
                    <span class="db-stat-label">Total Users</span>
                    <span class="db-stat-value">{{ $totalUsers }}</span>
                </div>
                <div class="db-stat-icon db-icon-tasks">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <div class="db-stat-card">
                <div class="db-stat-info">
                    <span class="db-stat-label">Total Tasks</span>
                    <span class="db-stat-value">{{ $totalTasksCreated }}</span>
                </div>
                <div class="db-stat-icon db-icon-completed">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
            </div>
            <div class="db-stat-card">
                <div class="db-stat-info">
                    <span class="db-stat-label">Tasks Done</span>
                    <span class="db-stat-value">
                        {{ $totalTasksCompleted }}
                        <span class="text-xs text-[#22c55e] font-normal ml-1">
                            ({{ $totalTasksCreated > 0 ? round(($totalTasksCompleted / $totalTasksCreated) * 100) : 0 }}%)
                        </span>
                    </span>
                </div>
                <div class="db-stat-icon db-icon-active">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                </div>
            </div>
            <div class="db-stat-card">
                <div class="db-stat-info">
                    <span class="db-stat-label">Active Users</span>
                    <span class="db-stat-value">{{ $activeUsers }}</span>
                </div>
                <div class="db-stat-icon db-icon-high">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
            </div>
        </div>

        <div class="db-main" style="grid-template-columns: 1fr; gap: 24px;">
            <div class="db-admin-grid-10">
                <!-- Left column: 2 stacked cards, full height -->
                <div class="db-admin-col-3 flex flex-col gap-4">
                    <div class="db-admin-card db-admin-card-center flex flex-col justify-center flex-grow">
                        <div class="db-admin-card-title-sm">Highest Streak</div>
                        <div class="db-admin-card-value-lg">🔥 {{ $highestStreak }}</div>
                    </div>
                    <div class="db-admin-card db-admin-card-center flex flex-col justify-center flex-grow">
                        <div class="db-admin-card-title-sm">Average Streak</div>
                        <div class="db-admin-card-value-lg">⚡ {{ round($averageStreak, 1) }}</div>
                    </div>
                </div>
                <!-- Right column: User Growth graph -->
                <div class="db-admin-col-7 db-admin-card">
                    <h3 class="db-admin-chart-title">User Growth</h3>
                    <div class="db-admin-chart-container">
                        @foreach($userGrowth as $date => $count)
                            <div class="flex flex-col items-center flex-1 group">
                                <div class="relative flex items-end w-full h-full">
                                    <div class="db-admin-chart-bar" style="height: {{ ($count / (max($userGrowth) ?: 1)) * 100 }}%">
                                        <div class="db-admin-chart-bar-tooltip">{{ $count }} users</div>
                                    </div>
                                </div>
                                <span class="db-admin-chart-label">{{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="db-admin-grid-2">
                <div class="db-admin-card">
                    <h3 class="db-admin-chart-title">Task Priority Distribution</h3>
                    <div class="db-admin-priority-bar-group">
                        @foreach([
                            'High' => ['#ef4444', $priorityChart['High']],
                            'Medium' => ['#f59e0b', $priorityChart['Medium']],
                            'Low' => ['#3b82f6', $priorityChart['Low']]
                        ] as $label => $data)
                            <div class="db-admin-priority-bar-item">
                                <div class="db-admin-priority-bar-header">
                                    <span class="db-admin-priority-bar-name">{{ $label }}</span>
                                    <span class="db-admin-priority-bar-count">{{ $data[1] }}</span>
                                </div>
                                <div class="db-admin-priority-bar">
                                    <div class="db-admin-priority-bar-fill" style="width: {{ ($data[1] / (array_sum($priorityChart) ?: 1)) * 100 }}%; background: {{ $data[0] }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="db-admin-card">
                    <h3 class="db-admin-chart-title">Activity Heatmap</h3>
                    <div class="db-admin-heatmap-grid">
                        @foreach($activityHeatmap as $date => $count)
                            <div class="group relative">
                                <div class="db-admin-heatmap-cell" style="background: rgba(34,197,94,{{ 0.15 + (($count / (max($activityHeatmap) ?: 1)) * 0.85) }})">
                                    <div class="db-admin-heatmap-tooltip">{{ $count }} tasks on {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</div>
                                </div>
                                <span class="db-admin-chart-label">{{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div>
                <h2 class="db-header-title" style="margin-bottom: 16px; font-size: 22px;">Achievement Analytics</h2>
                <div class="db-admin-grid-3">
                    <div class="db-admin-card db-admin-card-center">
                        <div class="db-admin-card-title-sm">Most Unlocked</div>
                        @if($mostUnlocked)
                            <img src="{{ asset('AchievementBadges/' . $mostUnlocked->icon) }}" alt="{{ $mostUnlocked->name }}" class="db-admin-achievement-icon">
                            <div class="db-admin-achievement-name">{{ $mostUnlocked->name }}</div>
                            <div class="db-admin-achievement-count">{{ $mostUnlocked->users_count }} unlocks</div>
                        @endif
                    </div>
                    <div class="db-admin-card db-admin-card-center">
                        <div class="db-admin-card-title-sm">Rarest Achievement</div>
                        @if($rarest)
                            <img src="{{ asset('AchievementBadges/' . $rarest->icon) }}" alt="{{ $rarest->name }}" class="db-admin-achievement-icon">
                            <div class="db-admin-achievement-name">{{ $rarest->name }}</div>
                            <div class="db-admin-achievement-count db-admin-achievement-count-rare">{{ $rarest->users_count }} unlocks</div>
                        @endif
                    </div>
                    <div class="db-admin-card db-admin-card-center">
                        <div class="db-admin-card-title-sm">Average Achievement</div>
                        <div class="db-admin-card-value-lg">🏆 {{ round($averageAchievements, 0) }}</div>
                        <div class="db-admin-card-title-sm" style="margin-bottom: 0; margin-top: 8px;">Per User</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.db-admin-achievement-icon {
    width: 64px;
    height: 64px;
    object-fit: contain;
    margin: 12px auto;
}
</style>
