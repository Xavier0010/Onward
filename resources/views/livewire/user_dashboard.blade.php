<div class="db-layout">
    <x-sidebar active="dashboard"/>

    <div class="db-root">
        <header class="db-header flex justify-between items-center w-full mb-[20px]">
            <h1 class="db-header-title">Dashboard</h1>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-[#1a1d27] border border-[#2a2d3e] px-4 py-2 rounded-xl text-[#22c55e] font-bold shadow-sm">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    <span>{{ $weeklyXp }} XP (Weekly)</span>
                </div>
                <div class="flex items-center gap-2 bg-[#1a1d27] border border-[#2a2d3e] px-4 py-2 rounded-xl text-[#f59e0b] font-bold shadow-sm">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    <span>{{ $availableXp }} XP (Available)</span>
                </div>
            </div>
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
                        class="new-task-btn"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
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
                            <button
                                wire:click="$set('showFilterModal', true)"
                                class="sort-filter-btn"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                                Sort & Filter
                            </button>
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
                            class="db-task-item db-priority-{{ $task->priority }} {{ $isCompleted ? 'db-task-done' : '' }} cursor-pointer"
                            wire:key="task-{{ $task->id }}"
                            wire:click="openDetailsModal({{ $task->id }})"
                        >

                            {{-- LEFT SIDE --}}
                            <div class="flex items-center gap-3 flex-1 min-w-0">

                                {{-- Check Button --}}
                                <button
                                    wire:click.stop="toggleTask({{ $task->id }})"
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
                                        @if($task->end_date)
                                            @php
                                                $now = now()->startOfDay();
                                                $end = $task->end_date->startOfDay();
                                                $isPast = $now->greaterThan($end);
                                                
                                                $diff = $now->diff($end);
                                                $years = $diff->y;
                                                $months = $diff->m;
                                                $days = $diff->d;
                                                
                                                $parts = [];
                                                if($years > 0) $parts[] = $years . ' ' . Str::plural('year', $years);
                                                if($months > 0) $parts[] = $months . ' ' . Str::plural('month', $months);
                                                if($days > 0 && $years == 0) $parts[] = $days . ' ' . Str::plural('day', $days); // Only show days if years = 0, or just show it all. The user said "1 year 15 days ago", wait, "1 month and 3 days left", "1 year 15 days ago".
                                                // Actually let's just push all non-zero.
                                                $parts = [];
                                                if($years > 0) $parts[] = $years . ' ' . ($years > 1 ? 'years' : 'year');
                                                if($months > 0) $parts[] = $months . ' ' . ($months > 1 ? 'months' : 'month');
                                                if($days > 0) $parts[] = $days . ' ' . ($days > 1 ? 'days' : 'day');
                                                
                                                if(empty($parts)) {
                                                    $diffStr = 'Today';
                                                } else {
                                                    $diffStr = implode(' and ', $parts) . ($isPast ? ' ago' : ' left');
                                                }
                                            @endphp
                                            Ends in: {{ $task->end_date->format('M d, Y') }}, {{ $diffStr }}
                                        @endif
                                    </span>
                                </div>

                            </div>

                            {{-- Status --}}
                            <div class="flex items-center gap-3">
                                @if($task->priority == 3)
                                    <button type="button" wire:click.stop="rollPriority({{ $task->id }})" class="db-badge db-badge-high cursor-pointer border-0">
                                        High
                                    </button>

                                @elseif($task->priority == 2)
                                    <button type="button" wire:click.stop="rollPriority({{ $task->id }})" class="db-badge db-badge-medium cursor-pointer border-0">
                                        Medium
                                    </button>

                                @else
                                    <button type="button" wire:click.stop="rollPriority({{ $task->id }})" class="db-badge db-badge-low cursor-pointer border-0">
                                        Low
                                    </button>
                                @endif

                                @if($task->status == 2)
                                    <button type="button" wire:click.stop="rollStatus({{ $task->id }})" class="db-badge db-badge-progress cursor-pointer border-0">
                                        In Progress
                                    </button>

                                @elseif($task->status == 3)
                                    <span class="db-badge db-badge-done">
                                        Completed
                                    </span>

                                @else
                                    <button type="button" wire:click.stop="rollStatus({{ $task->id }})" class="db-badge db-badge-pending cursor-pointer border-0">
                                        Pending
                                    </button>
                                @endif

                                {{-- Hover Actions --}}
                                <div class="db-task-actions">

                                    {{-- Edit --}}
                                    <button
                                        wire:click.stop="editTask({{ $task->id }})"
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
                                        wire:click.stop="openConfirmModal('deleteTask', 'Delete Task?', 'Are you sure you want to delete this task? This action cannot be undone.', {{ $task->id }})"
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

                {{-- Quote --}}
                <div class="db-quote-card cursor-pointer" wire:click="refreshQuote" wire:poll.300s="refreshQuote">
                    <svg class="db-quote-mark" width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1zm12 0c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                    <p class="db-quote-text">{{ $quote['text'] }}</p>
                    <p class="db-quote-author">— {{ $quote['author'] }}</p>
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
                </div>
                <div class="db-lb-list">
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
        
                    <button wire:click="closeTaskModal" class="text-gray-400 hover:text-white cursor-pointer">
                        ✕
                    </button>
                </div>
        
                <form wire:submit.prevent="{{ $taskId ? 'updateTask' : 'createTask' }}">
        
                    <div class="space-y-4">
        
                        <div>
                            <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">
                                Task
                            </label>
        
                            <input
                                type="text"
                                wire:model="task"
                                class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e]"
                            >
                            @error('task') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
        
                        <div>
                            <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">
                                Description
                            </label>
        
                            <textarea
                                wire:model="description"
                                rows="4"
                                class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e]"
                            ></textarea>
                            @error('description') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
        
                        <div class="grid grid-cols-2 gap-4">
        
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">
                                    Priority
                                </label>
        
                                <div class="relative">
                                    <select
                                        wire:model="priority"
                                        class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e] cursor-pointer appearance-none"
                                    >
                                        <option value="1">Low</option>
                                        <option value="2">Medium</option>
                                        <option value="3">High</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                                @error('priority') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
        
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">
                                    Status
                                </label>
        
                                <div class="relative">
                                    <select
                                        wire:model="status"
                                        class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e] cursor-pointer appearance-none"
                                    >
                                        <option value="1">Pending</option>
                                        <option value="2">In Progress</option>
                                        @if(!$taskId)
                                            <option value="3">Completed</option>
                                        @endif
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                                @error('status') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
        
                        </div>
        
                        <div class="grid grid-cols-2 gap-4">
        
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">
                                    Start Date
                                </label>
        
                                <input
                                    type="date"
                                    wire:model="start_date"
                                    class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e] cursor-text"
                                    style="color-scheme: dark;"
                                >
                                @error('start_date') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
        
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-1 font-medium uppercase tracking-wider">
                                    End Date
                                </label>
        
                                <input
                                    type="date"
                                    wire:model="end_date"
                                    class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e] cursor-text"
                                    style="color-scheme: dark;"
                                >
                                @error('end_date') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
        
                        </div>

                        <div>
                            <div class="flex items-center gap-2 mb-1 relative group">
                                <label class="block text-[10px] text-gray-400 font-medium uppercase tracking-wider mb-0">
                                    Attachment (Max 10MB)
                                </label>
                                <svg class="w-3.5 h-3.5 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <!-- Tooltip -->
                                <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block w-64 bg-[#1f2235] text-xs text-gray-300 p-3 rounded-lg border border-[#2a2d3e] shadow-xl z-50">
                                    <span class="font-semibold text-white block mb-1">Supported Extensions:</span>
                                    Images: jpeg, jpg, png<br>
                                    Docs: doc, docx, pdf, txt, md, csv, xlsx, ppt, pptx<br>
                                    Archives: zip
                                </div>
                            </div>
        
                            <input
                                type="file"
                                wire:model="file"
                                accept=".jpeg,.jpg,.png,.xlsx,.zip,.txt,.md,.csv,.doc,.docx,.ppt,.pptx,.pdf"
                                class="w-full bg-[#141923] border border-[#343b4f] text-white rounded-lg py-3 px-4 transition-all duration-200 focus:outline-none focus:border-[#22c55e] focus:ring-1 focus:ring-[#22c55e] file:bg-[#2a2d3e] file:border-0 file:text-white file:rounded file:mr-3 file:cursor-pointer"
                            >
                            @error('file') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
        
                    </div>
        
                    <div class="flex justify-end gap-3 mt-6">
        
                        <button
                            type="button"
                            wire:click="closeTaskModal"
                            class="px-4 py-2 rounded-lg border border-[#2a2d3e] text-gray-300 cursor-pointer hover:bg-[#2a2d3e]"
                        >
                            Cancel
                        </button>
        
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg bg-[#22c55e] text-white cursor-pointer hover:bg-[#1ea951]"
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

                    <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-white cursor-pointer">
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

                    @if($selectedTask->file_path)
                    <div>
                        <div class="text-gray-500 mb-1">Attachment</div>
                        <div class="text-white mt-2">
                            @php
                                $ext = strtolower(pathinfo($selectedTask->file_path, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                            @endphp
                            
                            @if($isImage)
                                <div class="mb-3">
                                    <a href="{{ Storage::url($selectedTask->file_path) }}" target="_blank">
                                        <img src="{{ Storage::url($selectedTask->file_path) }}" class="w-full max-h-[250px] object-cover rounded-lg border border-[#2a2d3e] hover:opacity-90 transition-opacity cursor-pointer" alt="Attachment Preview">
                                    </a>
                                </div>
                            @endif

                            <a href="{{ Storage::url($selectedTask->file_path) }}" download="{{ $selectedTask->original_filename }}" class="inline-flex items-center gap-2 bg-[#1f2235] border border-[#343b4f] px-4 py-2 rounded-lg text-[#22c55e] hover:bg-[#2a2d3e] transition-colors" target="_blank">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span class="truncate max-w-[200px]">{{ $selectedTask->original_filename ?: 'Download File' }}</span>
                            </a>
                        </div>
                    </div>
                    @endif

                </div>

            </div>

        </div>
        @endif

        @if($showFilterModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="w-full max-w-xl bg-[#1a1d27] border border-[#2a2d3e] rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">Sort & Filter</h2>
                    <button wire:click="$set('showFilterModal', false)" class="text-gray-400 hover:text-white cursor-pointer transition">✕</button>
                </div>

                <div class="space-y-6">
                    {{-- Sort Section --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4 border-b border-[#2a2d3e] pb-2">Sort By</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="flex flex-col gap-3">
                                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="radio" wire:model="sortBy" value="task" class="text-[#22c55e] focus:ring-[#22c55e] bg-[#0f1117] border-[#2a2d3e] h-4 w-4"> Name
                                    </label>
                                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="radio" wire:model="sortBy" value="status" class="text-[#22c55e] focus:ring-[#22c55e] bg-[#0f1117] border-[#2a2d3e] h-4 w-4"> Status
                                    </label>
                                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="radio" wire:model="sortBy" value="priority" class="text-[#22c55e] focus:ring-[#22c55e] bg-[#0f1117] border-[#2a2d3e] h-4 w-4"> Priority
                                    </label>
                                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="radio" wire:model="sortBy" value="start_date" class="text-[#22c55e] focus:ring-[#22c55e] bg-[#0f1117] border-[#2a2d3e] h-4 w-4"> Start Date
                                    </label>
                                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="radio" wire:model="sortBy" value="end_date" class="text-[#22c55e] focus:ring-[#22c55e] bg-[#0f1117] border-[#2a2d3e] h-4 w-4"> End Date
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 block mb-3 font-medium uppercase tracking-wider">Direction</label>
                                <div class="flex flex-col gap-3">
                                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="radio" wire:model="sortDir" value="asc" class="text-[#22c55e] focus:ring-[#22c55e] bg-[#0f1117] border-[#2a2d3e] h-4 w-4"> Ascending
                                    </label>
                                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="radio" wire:model="sortDir" value="desc" class="text-[#22c55e] focus:ring-[#22c55e] bg-[#0f1117] border-[#2a2d3e] h-4 w-4"> Descending
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters Section --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4 border-b border-[#2a2d3e] pb-2">Filters</h3>
                        
                        <div class="space-y-5">
                            {{-- Status Filter --}}
                            <div>
                                <label class="text-xs text-gray-500 block mb-2 font-medium">Status</label>
                                <div class="flex flex-wrap gap-4">
                                    <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="checkbox" wire:model="filterStatuses" value="1" class="rounded border-[#2a2d3e] bg-[#0f1117] text-[#22c55e] focus:ring-[#22c55e] h-4 w-4">
                                        Pending
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="checkbox" wire:model="filterStatuses" value="2" class="rounded border-[#2a2d3e] bg-[#0f1117] text-[#22c55e] focus:ring-[#22c55e] h-4 w-4">
                                        In Progress
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="checkbox" wire:model="filterStatuses" value="3" class="rounded border-[#2a2d3e] bg-[#0f1117] text-[#22c55e] focus:ring-[#22c55e] h-4 w-4">
                                        Done
                                    </label>
                                </div>
                            </div>

                            {{-- Priority Filter --}}
                            <div>
                                <label class="text-xs text-gray-500 block mb-2 font-medium">Priority</label>
                                <div class="flex flex-wrap gap-4">
                                    <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="checkbox" wire:model="filterPriorities" value="3" class="rounded border-[#2a2d3e] bg-[#0f1117] text-[#22c55e] focus:ring-[#22c55e] h-4 w-4">
                                        High
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="checkbox" wire:model="filterPriorities" value="2" class="rounded border-[#2a2d3e] bg-[#0f1117] text-[#22c55e] focus:ring-[#22c55e] h-4 w-4">
                                        Medium
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer hover:text-white transition">
                                        <input type="checkbox" wire:model="filterPriorities" value="1" class="rounded border-[#2a2d3e] bg-[#0f1117] text-[#22c55e] focus:ring-[#22c55e] h-4 w-4">
                                        Low
                                    </label>
                                </div>
                            </div>

                            {{-- End Date Range --}}
                            <div>
                                <label class="text-xs text-gray-500 block mb-2 font-medium">End Date Range</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="date" wire:model="filterDateFrom" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-3 py-2 text-white text-sm cursor-text focus:ring-[#22c55e] focus:border-[#22c55e]" placeholder="From">
                                    <input type="date" wire:model="filterDateTo" class="w-full bg-[#0f1117] border border-[#2a2d3e] rounded-lg px-3 py-2 text-white text-sm cursor-text focus:ring-[#22c55e] focus:border-[#22c55e]" placeholder="To">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-5 border-t border-[#2a2d3e]">
                    <button wire:click="resetFilters" class="px-5 py-2 rounded-lg border border-[#2a2d3e] text-gray-300 cursor-pointer hover:bg-[#2a2d3e] transition text-sm font-medium">
                        Reset
                    </button>
                    <button wire:click="applyFilters" class="px-5 py-2 rounded-lg bg-[#22c55e] text-white cursor-pointer hover:bg-[#1ea951] transition shadow-lg shadow-green-500/20 text-sm font-medium">
                        Apply
                    </button>
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
/* ================================================================
   DASHBOARD — DARK THEME
   ================================================================ */
:root {
    --db-bg:          #0f1117;
    --db-surface:     #1a1d27;
    --db-surface-sidebar: #141720;
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

/* ── GENERIC DASHBOARD CARDS HOVER ── */
.db-stat-card {
    transition: transform 0.2s ease, border-color 0.2s ease;
}

.db-stat-card:hover {
    border-color: rgba(34, 197, 94, 0.5);
    transform: translateY(-2px);
    cursor: default;
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
.new-task-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    height: 38px;
    border-radius: 8px;
    border: 1px solid transparent;
    background: #22c55e;
    color: #fff;

    font-size: 14px;
    font-weight: 500;
    line-height: 1;

    cursor: pointer;
    transition: all .2s ease;
}

.new-task-btn:hover {
    background: #1ea951;
}

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

.sort-filter-btn {
    display: flex;
    align-items: center;
    gap: 8px;

    height: 38px;
    padding: 0 16px;

    border-radius: 8px;
    border: 1px solid #2a2d3e;

    background: #1f2235;
    color: #fff;

    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
}

.new-task-btn,
.sort-filter-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 38px;
    padding: 0 16px;

    font-size: 14px;
    font-weight: 500;
    line-height: 14px;

    box-sizing: border-box;
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

.db-badge-high   { background: #ef444420; color: #ef4444; }
.db-badge-medium { background: #eab30820; color: #eab308; }
.db-badge-low    { background: #22c55e20; color: #22c55e; }

.db-empty { text-align: center; color: var(--db-muted); padding: 32px 0; font-size: 14px; }

/* ── SIDEBAR ── */
.db-sidebar { display: flex; flex-direction: column; gap: 16px; height: 100%; min-height: 0; }

/* ── STREAK ── */
.db-streak-card {
    background: var(--db-surface-sidebar);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 24px;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    cursor: default;
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
.db-streak-label { font-size: 11px; font-weight: 700; letter-spacing: .12em; color: var(--db-muted); margin-bottom: 4px; }
.db-streak-sub   { font-size: 12px; color: var(--db-muted); }

/* ── LEADERBOARD ── */
.db-leaderboard {
    background: var(--db-surface-sidebar);
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
    background: var(--db-surface-sidebar);
    border: 1px solid var(--db-border);
    border-radius: var(--db-radius);
    padding: 20px;
    position: relative;
}
.db-quote-mark { color: #2a2d3e; position: absolute; bottom: 10px; right: 10px; }
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
    max-width: 0;
    overflow: hidden;

    transition:
        opacity .18s ease,
        transform .18s ease,
        max-width .18s ease;
}

.db-task-item:hover .db-task-actions {
    opacity: 1;
    transform: translateX(0);
    max-width: 150px;
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

/* Pagination */
.pagination-nav button,
.pagination-nav span {
    cursor: pointer;
}
</style>

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
