<div class="db-layout">
    <x-sidebar />

    <div class="db-root">
        <header class="db-header">
            <h1 class="db-header-title">Dashboard</h1>
        </header>


        {{-- ═══════════════════════════ STAT CARDS ═══════════════════════════ --}}
        <div class="db-stats">
            <div class="db-stat-card">
                <div class="db-stat-info">
                    <span class="db-stat-label">Total Tasks</span>
                    <span class="db-stat-value">{{ $totalTasks }}</span>
                </div>
                <div class="db-stat-icon db-icon-tasks">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
            </div>
            <div class="db-stat-card">
                <div class="db-stat-info">
                    <span class="db-stat-label">Completed Tasks</span>
                    <span class="db-stat-value">{{ $completedTasks }}</span>
                </div>
                <div class="db-stat-icon db-icon-completed">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                </div>
            </div>
            <div class="db-stat-card">
                <div class="db-stat-info">
                    <span class="db-stat-label">Active Tasks</span>
                    <span class="db-stat-value">{{ $activeTasks }}</span>
                </div>
                <div class="db-stat-icon db-icon-active">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                </div>
            </div>
            <div class="db-stat-card">
                <div class="db-stat-info">
                    <span class="db-stat-label">High Priority</span>
                    <span class="db-stat-value">{{ $highPriority }}</span>
                </div>
                <div class="db-stat-icon db-icon-high">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════ MAIN CONTENT ═══════════════════════════ --}}
        <div class="db-main">

            {{-- ─── LEFT: TO-DO LIST ─── --}}
            <div class="db-todo">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="db-todo-title m-0">To-do</h2>
                    <button
                        wire:click="openCreateModal"
                        class="bg-[#22c55e] hover:bg-[#1ea951] text-white px-3 py-1.5 rounded-lg font-medium transition text-sm flex items-center gap-1.5 cursor-pointer"
                    >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        New task
                    </button>
                </div>

                {{-- Search + Filters --}}
                <div class="flex flex-col gap-3 mb-5">
                    <div class="db-controls">
                        <div class="db-search-wrap">
                            <svg class="db-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input
                                type="text"
                                wire:model.live="searchQuery"
                                placeholder="Search your task here!"
                                class="db-search"
                            />
                        </div>
                        <div class="db-filter-group">
                            <div class="db-filter-wrap">
                                <select wire:model.live="filterPriority" class="db-filter">
                                    <option value="">Priority</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                                <svg class="db-filter-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                            </div>
                            <div class="db-filter-wrap">
                                <select wire:model.live="filterStatus" class="db-filter">
                                    <option value="">Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <svg class="db-filter-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Task List --}}
                <div class="db-task-list">

                    @forelse($paginatedTasks as $task)

                        @php
                            $isCompleted = $task->status == 3;
                        @endphp

                        <div
                            class="db-task-item db-priority-{{ $task->priority }} {{ $isCompleted ? 'db-task-done' : '' }}"
                            wire:key="task-{{ $task->id }}"
                        >

                            {{-- LEFT SIDE --}}
                            <div class="flex items-center gap-3 flex-1 min-w-0">

                                {{-- Check Button --}}
                                <button
                                    wire:click="toggleTask({{ $task->id }})"
                                    class="db-task-check {{ $isCompleted ? 'db-check-done' : '' }}"
                                    aria-label="Toggle task"
                                >
                                    @if($isCompleted)
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="3">
                                            <path d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </button>

                                {{-- Task Content --}}
                                <div class="flex flex-col min-w-0">
                                    <span class="db-task-title {{ $isCompleted ? 'db-task-strikethrough' : '' }}">
                                        {{ $task->task }}
                                    </span>

                                    <span class="db-task-due">
                                        {{ optional($task->end_date)->format('M d, Y') }}
                                    </span>
                                </div>

                            </div>

                            {{-- Status --}}
                            <div class="flex items-center gap-3">

                                @if($task->status == 2)
                                    <span class="db-badge db-badge-progress">
                                        In Progress
                                    </span>

                                @elseif($task->status == 3)
                                    <span class="db-badge db-badge-done">
                                        Completed
                                    </span>

                                @else
                                    <span class="db-badge db-badge-pending">
                                        Pending
                                    </span>
                                @endif

                                {{-- Hover Actions --}}
                                <div class="db-task-actions">

                                    {{-- Details --}}
                                    <button
                                        wire:click="openDetailsModal({{ $task->id }})"
                                        class="db-icon-btn db-view-btn"
                                        title="View Details"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>

                                    {{-- Edit --}}
                                    <button
                                        wire:click="editTask({{ $task->id }})"
                                        class="db-icon-btn db-edit-btn"
                                        title="Edit Task"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                        </svg>
                                    </button>

                                    {{-- Delete --}}
                                    <button
                                        wire:click="deleteTask({{ $task->id }})"
                                        wire:confirm="Delete this task?"
                                        class="db-icon-btn db-delete-btn"
                                        title="Delete Task"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6"/>
                                            <path d="M14 11v6"/>
                                            <path d="M9 6V4h6v2"/>
                                        </svg>
                                    </button>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="db-empty">
                            No tasks match your filters.
                        </div>

                    @endforelse

                </div>

                {{-- Pagination --}}
                @if($paginatedTasks->hasPages())
                    <div class="pt-4 border-t border-[#2a2d3e]">
                        {{ $paginatedTasks->links() }}
                    </div>
                @endif
            </div>

            {{-- ─── RIGHT: STREAK + LEADERBOARD + QUOTE ─── --}}
            <div class="db-sidebar">

                {{-- Daily Streak --}}
                <div class="db-streak-card">
                    <div class="db-streak-flame">
                        <span class="db-flame-emoji">🔥</span>
                        <span class="db-streak-num">{{ $streak }}</span>
                    </div>
                    <div class="db-streak-label">DAY STREAK</div>
                        @if ($streak == 0)
                            <div class="db-streak-sub">Start your daily streak!</div>
                        @elseif ($streak <= 7)
                            <div class="db-streak-sub">Stay consistent!</div>
                        @else
                            <div class="db-streak-sub">Keep it up! You're on fire🔥</div>
                        @endif
                </div>

                {{-- Leaderboard --}}
                <div class="db-leaderboard">
                    <div class="db-lb-header">
                        <div class="db-lb-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 000 5H6"/><path d="M18 9h1.5a2.5 2.5 0 010 5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0012 0V2z"/></svg>
                            Leaderboard
                        </div>
                        <span class="db-lb-period">Weekly XP</span>
                    </div>
                    <div class="db-lb-list">
                        @foreach($leaderboard as $entry)
                            <div class="db-lb-row {{ $entry['rank'] <= 3 ? 'db-lb-top3' : '' }}">
                                <div class="db-lb-rank">
                                    @if($entry['medal'] === 'gold')
                                        <span class="db-medal db-medal-gold">🥇</span>
                                    @elseif($entry['medal'] === 'silver')
                                        <span class="db-medal db-medal-silver">🥈</span>
                                    @elseif($entry['medal'] === 'bronze')
                                        <span class="db-medal db-medal-bronze">🥉</span>
                                    @else
                                        <span class="db-rank-num">{{ $entry['rank'] }}</span>
                                    @endif
                                </div>
                                <div class="db-lb-avatar" style="background: {{ $entry['color'] }}20; border-color: {{ $entry['color'] }}40; color: {{ $entry['color'] }}">
                                    {{ $entry['avatar'] }}
                                </div>
                                <span class="db-lb-name">{{ $entry['name'] }}</span>
                                <span class="db-lb-xp {{ $entry['rank'] <= 3 ? 'db-xp-highlight' : '' }}">
                                    {{ number_format($entry['xp']) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Quote --}}
                <div class="db-quote-card">
                    <svg class="db-quote-mark" width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1zm12 0c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                    <p class="db-quote-text">{{ $quote['text'] }}</p>
                    <p class="db-quote-author">— {{ $quote['author'] }}</p>
                </div>

            </div>
        </div>

        @if($showTaskModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        
            <div class="w-full max-w-xl bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">
        
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">
                        {{ $taskId ? 'Edit Task' : 'Create Task' }}
                    </h2>
        
                    <button wire:click="closeTaskModal" class="text-gray-400 hover:text-white">
                        ✕
                    </button>
                </div>
        
                <form wire:submit.prevent="{{ $taskId ? 'updateTask' : 'createTask' }}">
        
                    <div class="space-y-4">
        
                        <div>
                            <label class="text-sm text-gray-400 block mb-1">
                                Task
                            </label>
        
                            <input
                                type="text"
                                wire:model="task"
                                class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white"
                            >
                        </div>
        
                        <div>
                            <label class="text-sm text-gray-400 block mb-1">
                                Description
                            </label>
        
                            <textarea
                                wire:model="description"
                                rows="4"
                                class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white"
                            ></textarea>
                        </div>
        
                        <div class="grid grid-cols-2 gap-4">
        
                            <div>
                                <label class="text-sm text-gray-400 block mb-1">
                                    Priority
                                </label>
        
                                <select
                                    wire:model="priority"
                                    class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white"
                                >
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                </select>
                            </div>
        
                            <div>
                                <label class="text-sm text-gray-400 block mb-1">
                                    Status
                                </label>
        
                                <select
                                    wire:model="status"
                                    class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white"
                                >
                                    <option value="1">Pending</option>
                                    <option value="2">In Progress</option>
                                    <option value="3">Completed</option>
                                </select>
                            </div>
        
                        </div>
        
                        <div class="grid grid-cols-2 gap-4">
        
                            <div>
                                <label class="text-sm text-gray-400 block mb-1">
                                    Start Date
                                </label>
        
                                <input
                                    type="date"
                                    wire:model="start_date"
                                    class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white"
                                >
                            </div>
        
                            <div>
                                <label class="text-sm text-gray-400 block mb-1">
                                    End Date
                                </label>
        
                                <input
                                    type="date"
                                    wire:model="end_date"
                                    class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-4 py-3 text-white"
                                >
                            </div>
        
                        </div>
        
                    </div>
        
                    <div class="flex justify-end gap-3 mt-6">
        
                        <button
                            type="button"
                            wire:click="closeTaskModal"
                            class="px-4 py-2 rounded-lg border border-[#2a2d3e] text-gray-300"
                        >
                            Cancel
                        </button>
        
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg bg-[#22c55e] text-white"
                        >
                            {{ $taskId ? 'Update Task' : 'Create Task' }}
                        </button>
        
                    </div>
        
                </form>
        
            </div>
        
        </div>
        @endif

        @if($showDetailsModal && $selectedTask)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">

            <div class="w-full max-w-lg bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6">

                        <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">
                        Task Details
                    </h2>

                    <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-white">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 text-sm">

                    <div>
                        <div class="text-gray-500 mb-1">Task</div>
                        <div class="text-white">{{ $selectedTask->task }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500 mb-1">Description</div>
                        <div class="text-white">
                            {{ $selectedTask->description ?: 'No description' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div>
                            <div class="text-gray-500 mb-1">Priority</div>

                            <div class="text-white">
                                @if($selectedTask->priority == 1)
                                    Low
                                @elseif($selectedTask->priority == 2)
                                    Medium
                                @else
                                    High
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500 mb-1">Status</div>

                            <div class="text-white">
                                @if($selectedTask->status == 1)
                                    Pending
                                @elseif($selectedTask->status == 2)
                                    In Progress
                                @else
                                    Completed
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div>
                            <div class="text-gray-500 mb-1">Start Date</div>
                            <div class="text-white">
                                {{ optional($selectedTask->start_date)->format('M d, Y') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500 mb-1">End Date</div>
                            <div class="text-white">
                                {{ optional($selectedTask->end_date)->format('M d, Y') }}
                            </div>
                        </div>

                    </div>

                    <div>
                        <div class="text-gray-500 mb-1">Completed At</div>

                        <div class="text-white">
                            {{ optional($selectedTask->completed_at)->format('M d, Y h:i A') ?: 'Not completed yet' }}
                        </div>
                    </div>

                </div>

            </div>

        </div>
        @endif
    </div>
</div>

<style>
/* ================================================================
   DASHBOARD — DARK THEME
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
    overflow: hidden;
    background: var(--db-bg);
    color: var(--db-text);
    padding: 32px 40px;
    box-sizing: border-box;
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

/* ── STAT CARDS ── */
.db-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
    flex-shrink: 0;
}

.db-stat-card {
    background: var(--db-surface);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 20px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: border-color .2s, transform .2s;
}
.db-stat-card:hover {
    border-color: #3a3d50;
    transform: translateY(-1px);
}

.db-stat-info { display: flex; flex-direction: column; gap: 8px; }
.db-stat-label { font-size: 13px; color: var(--db-muted); font-weight: 500; }
.db-stat-value { font-size: 36px; font-weight: 700; line-height: 1; color: var(--db-text); }

.db-stat-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}
.db-icon-tasks    { background: #22c55e20; color: #22c55e; }
.db-icon-completed{ background: #22c55e20; color: #22c55e; }
.db-icon-active   { background: #3b82f620; color: #3b82f6; }
.db-icon-high     { background: #ef444420; color: #ef4444; }

/* ── MAIN LAYOUT ── */
.db-main {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: stretch;
    min-height: 0;
}

/* ── TO-DO SECTION ── */
.db-todo {
    background: var(--db-surface);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 24px;
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

.db-todo-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--db-text);
}

.db-controls {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.db-search-wrap {
    flex: 1;
    position: relative;
    min-width: 200px;
}
.db-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--db-muted);
}
.db-search {
    width: 100%;
    background: var(--db-surface2);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius-sm);
    padding: 9px 12px 9px 36px;
    color: var(--db-text);
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s;
}
.db-search::placeholder { color: var(--db-muted); }
.db-search:focus { border-color: var(--db-accent2); }

.db-filter-group { display: flex; gap: 8px; }
.db-filter-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.db-filter {
    appearance: none;
    background: var(--db-surface2);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius-sm);
    padding: 9px 32px 9px 12px;
    color: var(--db-text);
    font-size: 14px;
    cursor: pointer;
    outline: none;
    transition: border-color .2s;
}
.db-filter:focus { border-color: var(--db-accent2); }
.db-filter-arrow {
    position: absolute;
    right: 10px;
    color: var(--db-muted);
    pointer-events: none;
}

/* ── TASK ITEMS ── */
.db-task-list { flex: 1; display: flex; flex-direction: column; gap: 8px; justify-content: flex-start; overflow-y: auto; min-height: 0; padding-right: 4px; }

.db-task-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--db-surface2);
    border: 1px solid var(--db-border);
    border-left: 3px solid transparent;
    border-radius: var(--db-radius-sm);
    padding: 13px 16px;
    transition: background .15s, border-color .15s;
}
.db-task-item:hover { background: #242740; }

.db-priority-high   { border-left-color: var(--db-high); }
.db-priority-medium { border-left-color: var(--db-medium); }
.db-priority-low    { border-left-color: var(--db-low); }

.db-task-done { opacity: .65; }

.db-task-check {
    flex-shrink: 0;
    width: 22px; height: 22px;
    border-radius: 50%;
    border: 2px solid var(--db-border);
    background: transparent;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s, border-color .15s;
    padding: 0;
}
.db-task-check:hover { border-color: var(--db-accent); }
.db-check-done {
    background: var(--db-accent);
    border-color: var(--db-accent);
    color: #fff;
}

.db-task-title {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: var(--db-text);
}
.db-task-strikethrough { text-decoration: line-through; color: var(--db-muted); }

.db-task-due { font-size: 13px; color: var(--db-muted); white-space: nowrap; }

.db-badge {
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    white-space: nowrap;
}
.db-badge-progress { background: #3b82f620; color: #3b82f6; }
.db-badge-done     { background: #22c55e20; color: #22c55e; }
.db-badge-pending  { background: #64748b20; color: #94a3b8; }

.db-empty { text-align: center; color: var(--db-muted); padding: 32px 0; font-size: 14px; }

/* ── SIDEBAR ── */
.db-sidebar { display: flex; flex-direction: column; gap: 16px; height: 100%; min-height: 0; }

/* ── STREAK ── */
.db-streak-card {
    background: var(--db-surface);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 24px;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.db-streak-flame {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0px;
    margin-bottom: 6px;
}
.db-flame-emoji { font-size: 70px; line-height: 1; }
.db-streak-num  { font-size: 90px; font-weight: 900; color: var(--db-text); line-height: 1; }
.db-streak-label { font-size: 11px; font-weight: 700; letter-spacing: .12   em; color: var(--db-muted); margin-bottom: 4px; }
.db-streak-sub   { font-size: 12px; color: var(--db-muted); }

/* ── LEADERBOARD ── */
.db-leaderboard {
    background: var(--db-surface);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 20px;
}
.db-lb-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.db-lb-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 15px;
    font-weight: 700;
    color: var(--db-text);
}
.db-lb-period { font-size: 12px; color: var(--db-muted); }

.db-lb-list { display: flex; flex-direction: column; gap: 4px; }

.db-lb-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 6px;
    border-radius: var(--db-radius-sm);
    transition: background .15s;
}
.db-lb-row:hover { background: var(--db-surface2); }
.db-lb-top3 { }

.db-lb-rank { width: 24px; text-align: center; flex-shrink: 0; }
.db-medal   { font-size: 16px; }
.db-rank-num { font-size: 13px; color: var(--db-muted); font-weight: 600; }

.db-lb-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1.5px solid;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700;
    flex-shrink: 0;
}
.db-lb-name { flex: 1; font-size: 14px; font-weight: 500; color: var(--db-text); }
.db-lb-xp { font-size: 13px; font-weight: 700; color: var(--db-muted); }
.db-xp-highlight { color: var(--db-medium); }

/* ── QUOTE ── */
.db-quote-card {
    background: var(--db-surface);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 20px;
    position: relative;
}
.db-quote-mark { color: #2a2d3e; position: absolute; top: 14px; right: 14px; }
.db-quote-text {
    font-size: 13px;
    line-height: 1.6;
    color: #94a3b8;
    font-style: italic;
    margin: 0 0 8px;
}
.db-quote-author { font-size: 12px; color: var(--db-muted); margin: 0; }

/* ─────────────────────────────
   TASK ACTIONS
───────────────────────────── */

.db-task-item {
    position: relative;
}

.db-task-actions {
    display: flex;
    align-items: center;
    gap: 6px;

    opacity: 0;
    transform: translateX(6px);

    transition:
        opacity .18s ease,
        transform .18s ease;
}

.db-task-item:hover .db-task-actions {
    opacity: 1;
    transform: translateX(0);
}

.db-icon-btn {
    width: 32px;
    height: 32px;

    border: none;
    border-radius: 8px;

    display: flex;
    align-items: center;
    justify-content: center;

    cursor: pointer;

    transition:
        background .18s ease,
        transform .18s ease,
        color .18s ease;

    background: transparent;
}

.db-icon-btn:hover {
    transform: translateY(-1px);
}

.db-view-btn {
    color: #3b82f6;
}

.db-view-btn:hover {
    background: rgba(59, 130, 246, 0.12);
}

.db-edit-btn {
    color: #f59e0b;
}

.db-edit-btn:hover {
    background: rgba(245, 158, 11, 0.12);
}

.db-delete-btn {
    color: #ef4444;
}

.db-delete-btn:hover {
    background: rgba(239, 68, 68, 0.12);
}

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
    .db-stats { grid-template-columns: repeat(2, 1fr); }
    .db-main  { grid-template-columns: 1fr; }
    .db-sidebar { display: grid; grid-template-columns: 1fr 1fr 1fr; }
}
@media (max-width: 640px) {
    .db-root  { padding: 16px; }
    .db-stats { grid-template-columns: 1fr 1fr; }
    .db-sidebar { grid-template-columns: 1fr; }
}
</style>
