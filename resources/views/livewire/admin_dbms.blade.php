<div class="db-layout">
    <x-sidebar active="dbms"/>

    <div class="db-root">
        <header class="db-header flex justify-between items-center w-full mb-[20px]">
            <h1 class="db-header-title">Database Management</h1>
        </header>

        <div class="db-dbms-table-nav">
            @foreach($tables as $table)
                <button 
                    wire:click="selectTable('{{ $table }}')"
                    class="db-dbms-table-btn {{ $activeTable === $table ? 'active' : '' }}"
                >
                    {{ Str::studly(Str::singular($table)) }}
                </button>
            @endforeach
        </div>

        @if($activeTable)
        <div class="db-dbms-wrapper">
            <div class="db-dbms-header">
                <h2 class="db-dbms-title capitalize">{{ str_replace('_', ' ', $activeTable) }}</h2>
                <button
                    wire:click="openCreateModal"
                    class="new-task-btn"
                    wire:loading.attr="disabled"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    New Data
                </button>
            </div>

            <div class="db-dbms-search-wrap">
                <svg class="db-dbms-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Search {{ $activeTable }}..."
                    class="db-dbms-search"
                />
            </div>

            <div class="db-dbms-table-wrap">
                <div class="db-dbms-table-container">
                    <table class="db-dbms-table">
                        <thead>
                            <tr>
                                @foreach($columns as $col)
                                    <th wire:click="sortByCol('{{ $col }}')">
                                        <div class="flex items-center gap-2">
                                            {{ str_replace('_', ' ', $col) }}
                                            @if($sortBy === $col)
                                                <svg class="w-3 h-3 text-[#22c55e]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                            @endif
                                        </div>
                                    </th>
                                @endforeach
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr wire:click="showDetails({{ $record->id ?? 0 }})">
                                    @foreach($columns as $col)
                                        <td>{{ Str::limit($record->$col ?? '', 30) }}</td>
                                    @endforeach
                                    <td>
                                        <div class="db-dbms-actions">
                                            <button wire:click.stop="editRecord({{ $record->id ?? 0 }})" class="db-icon-btn db-dbms-btn-edit" title="Edit" wire:loading.attr="disabled">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </button>
                                            <button wire:click.stop="confirmDelete({{ $record->id ?? 0 }})" class="db-icon-btn db-dbms-btn-delete" title="Delete" wire:loading.attr="disabled">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columns) + 1 }}" class="db-dbms-empty">
                                        <svg class="db-dbms-empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        No data found in {{ str_replace('_', ' ', $activeTable) }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6">
                {{ $records->links() }}
            </div>
        </div>
        @endif

        @if($showModal)
        <div class="db-modal-backdrop" wire:click="$set('showModal', false)">
            <div class="db-modal" wire:click.stop>
                <div class="db-modal-header">
                    <h2 class="db-modal-title capitalize">
                        {{ $editId ? 'Edit' : 'Create' }} {{ str_replace('_', ' ', Str::singular($activeTable)) }}
                    </h2>
                    <button wire:click="$set('showModal', false)" class="db-modal-close" wire:loading.attr="disabled">✕</button>
                </div>
                <form wire:submit.prevent="saveRecord" class="db-modal-form">
                    <div class="db-modal-body">
                        @foreach($columns as $col)
                            @if(!in_array($col, ['id', 'created_at', 'updated_at']))
                                <div class="db-form-group">
                                    <label class="db-form-label">{{ str_replace('_', ' ', $col) }}</label>
                                    
                                    @php
                                        $colDetails = $columnDetails[$col] ?? ['type' => 'text'];
                                        $colType = $colDetails['type'] ?? 'text';
                                        $isForeignKey = isset($foreignKeys[$col]);
                                        $enumValues = $colDetails['enum_values'] ?? [];
                                    @endphp
                                    
                                    @if($isForeignKey)
                                        <select wire:model="formData.{{ $col }}" class="db-form-input">
                                            <option value="">Select {{ str_replace('_', ' ', Str::singular($foreignKeys[$col]['table'])) }}</option>
                                            @foreach($foreignKeys[$col]['options'] as $id => $value)
                                                <option value="{{ $id }}">{{ $id }}: {{ $value }}</option>
                                            @endforeach
                                        </select>
                                    @elseif(!empty($enumValues))
                                        <select wire:model="formData.{{ $col }}" class="db-form-input">
                                            @foreach($enumValues as $val)
                                                <option value="{{ $val }}">{{ ucfirst($val) }}</option>
                                            @endforeach
                                        </select>
                                    @elseif(in_array($colType, ['tinyint', 'smallint', 'int', 'integer', 'bigint']))
                                        {{-- Check if it's boolean (tinyint 1) --}}
                                        @if($colType === 'tinyint' && in_array($col, ['is_completed']))
                                            <select wire:model="formData.{{ $col }}" class="db-form-input">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        @else
                                            <input
                                                type="number"
                                                wire:model="formData.{{ $col }}"
                                                class="db-form-input"
                                            />
                                        @endif
                                    @elseif($colType === 'date')
                                        <input
                                            type="date"
                                            wire:model="formData.{{ $col }}"
                                            class="db-form-input"
                                        />
                                    @elseif($colType === 'datetime' || $colType === 'timestamp')
                                        <input
                                            type="datetime-local"
                                            wire:model="formData.{{ $col }}"
                                            class="db-form-input"
                                        />
                                    @elseif($colType === 'text' || $colType === 'json')
                                        <textarea
                                            wire:model="formData.{{ $col }}"
                                            class="db-form-input"
                                            rows="3"
                                        ></textarea>
                                    @elseif($colType === 'email')
                                        <input
                                            type="email"
                                            wire:model="formData.{{ $col }}"
                                            class="db-form-input"
                                        />
                                    @elseif($colType === 'password')
                                        <input
                                            type="password"
                                            wire:model="formData.{{ $col }}"
                                            class="db-form-input"
                                        />
                                    @else
                                        <input
                                            type="text"
                                            wire:model="formData.{{ $col }}"
                                            class="db-form-input"
                                        />
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="db-modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="db-btn db-btn-secondary" wire:loading.attr="disabled">Cancel</button>
                        <button type="submit" class="db-btn db-btn-primary" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:loading.class.remove="hover:bg-[#1ea951]">
                            <span wire:loading.remove>{{ $editId ? 'Update' : 'Save' }}</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($showDetailsModal && $selectedRecord)
        <div class="db-modal-backdrop" wire:click="$set('showDetailsModal', false)">
            <div class="db-modal" wire:click.stop>
                <div class="db-modal-header">
                    <h2 class="db-modal-title capitalize">{{ str_replace('_', ' ', Str::singular($activeTable)) }} Details</h2>
                    <button wire:click="$set('showDetailsModal', false)" class="db-modal-close" wire:loading.attr="disabled">✕</button>
                </div>
                <div class="db-modal-body">
                    <div class="db-details-group">
                        @foreach((array)$selectedRecord as $key => $value)
                            <div class="db-details-item">
                                <div class="db-details-label">{{ str_replace('_', ' ', $key) }}</div>
                                <div class="db-details-value">{{ $value === null ? 'NULL' : $value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($showDeleteModal)
        <div class="db-modal-backdrop" wire:click="$set('showDeleteModal', false)">
            <div class="db-modal db-modal-sm" wire:click.stop>
                <div class="db-modal-text-center">
                    <div class="db-modal-danger-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="db-modal-title" style="margin-bottom: 8px;">Delete Record?</h3>
                    <p class="text-gray-400 mb-6">Are you sure you want to delete this record? This action cannot be undone.</p>
                    <div class="flex gap-3 justify-center">
                        <button wire:click="$set('showDeleteModal', false)" class="db-btn db-btn-secondary" wire:loading.attr="disabled">Cancel</button>
                        <button wire:click="deleteRecord" class="db-btn db-btn-danger" wire:loading.attr="disabled" wire:loading.class="opacity-50">
                            <span wire:loading.remove>Yes, Delete</span>
                            <span wire:loading>Deleting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<style>
    .db-dbms-table-nav {
        display: flex;
        gap: 10px;
        width: 100%;
        padding-bottom: 12px;
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: #2a2d3e transparent;
        -webkit-overflow-scrolling: touch;
    }

    .db-dbms-table-nav::-webkit-scrollbar {
        height: 6px;
    }

    .db-dbms-table-nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .db-dbms-table-nav::-webkit-scrollbar-thumb {
        background: #2a2d3e;
        border-radius: 10px;
    }

    .db-dbms-table-btn {
        padding: 8px 20px;
        border-radius: 8px;
        border: 1px solid #2a2d3e;
        background: #1a1d27;
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all .2s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .db-dbms-table-btn:hover {
        background: #2a2d3e;
        color: white;
    }

    .db-dbms-table-btn.active {
        background: #22c55e;
        color: white;
        border-color: #22c55e;
        box-shadow: 0 0 15px rgba(34,197,94,0.3);
    }

    .db-dbms-wrapper {
        background: #1a1d27;
        border: 1px solid #2a2d3e;
        border-radius: 12px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        flex: 1;
        min-height: 0;
    }

    .db-dbms-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .db-dbms-title {
        font-size: 20px;
        font-weight: 700;
        color: #e2e8f0;
    }

    .db-dbms-search-wrap {
        position: relative;
        flex: 0;
        min-width: 250px;
    }

    .db-dbms-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
    }

    .db-dbms-search {
        width: 100%;
        padding: 9px 12px 9px 40px;
        background: #141923;
        border: 1px solid #2a2d3e;
        border-radius: 8px;
        color: #e2e8f0;
        font-size: 14px;
        outline: none;
        transition: border-color .2s;
    }

    .db-dbms-search:focus {
        border-color: #22c55e;
    }

    .db-dbms-table-wrap {
        border-radius: 12px;
        border: 1px solid #2a2d3e;
    }

    .db-dbms-table-container {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 450px;
    }

    .db-dbms-table-container::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    .db-dbms-table-container::-webkit-scrollbar-track {
        background: #0f1117;
    }

    .db-dbms-table-container::-webkit-scrollbar-thumb {
        background: #2a2d3e;
        border-radius: 10px;
    }

    .db-dbms-table {
        width: 100%;
        text-align: left;
        border-collapse: collapse;
        min-width: max-content;
    }

    .db-dbms-table thead tr {
        background: #0f1117;
        border-bottom: 1px solid #2a2d3e;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .db-dbms-table th {
        padding: 16px 24px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        transition: color .2s;
        background: #0f1117;
        white-space: nowrap;
    }

    .db-dbms-table th:hover {
        color: white;
    }

    .db-dbms-table tbody tr {
        border-bottom: 1px solid #2a2d3e;
        transition: background .2s;
        cursor: pointer;
    }

    .db-dbms-table tbody tr:hover {
        background: #2a2d3e;
    }

    .db-dbms-table td {
        padding: 16px 24px;
        color: #e2e8f0;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    .db-dbms-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        opacity: 0;
        transition: opacity .2s;
    }

    .db-dbms-table tbody tr:hover .db-dbms-actions {
        opacity: 1;
    }

    .db-dbms-btn-edit {
        color: #3b82f6;
        background: rgba(59,130,246,0.1);
    }

    .db-dbms-btn-edit:hover {
        background: rgba(59,130,246,0.2);
    }

    .db-dbms-btn-delete {
        color: #ef4444;
        background: rgba(239,68,68,0.1);
    }

    .db-dbms-btn-delete:hover {
        background: rgba(239,68,68,0.2);
    }

    .db-dbms-empty {
        padding: 48px;
        text-align: center;
        color: #64748b;
    }

    .db-dbms-empty-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 16px;
        color: #2a2d3e;
    }

    .db-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        padding: 16px;
        overflow-y: auto;
    }

    .db-modal {
        width: 100%;
        max-width: 672px;
        background: #1a1d27;
        border: 1px solid #2a2d3e;
        border-radius: 12px;
        padding: 24px;
        margin: auto;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    }

    .db-modal-sm {
        max-width: 448px;
    }

    .db-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        margin-bottom: 16px;
        border-bottom: 1px solid #2a2d3e;
    }

    .db-modal-title {
        font-size: 20px;
        font-weight: 700;
        color: #e2e8f0;
    }

    .db-modal-close {
        color: #64748b;
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 24px;
        line-height: 1;
        transition: color .2s;
    }

    .db-modal-close:hover {
        color: white;
    }

    .db-modal-body {
        max-height: 60vh;
        overflow-y: auto;
        padding-right: 8px;
    }

    .db-modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .db-modal-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .db-modal-body::-webkit-scrollbar-thumb {
        background: #2a2d3e;
        border-radius: 10px;
    }

    .db-modal-body::-webkit-scrollbar-thumb:hover {
        background: #343b4f;
    }

    .db-modal-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .db-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .db-form-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .db-form-input {
        padding: 12px 16px;
        background: #0f1117;
        border: 1px solid #2a2d3e;
        border-radius: 8px;
        color: #e2e8f0;
        font-size: 14px;
        outline: none;
        transition: border-color .2s;
    }

    .db-form-input:focus {
        border-color: #22c55e;
    }

    .db-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #2a2d3e;
    }

    .db-btn {
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        border: none;
    }

    .db-btn-secondary {
        background: transparent;
        border: 1px solid #2a2d3e;
        color: #64748b;
    }

    .db-btn-secondary:hover {
        background: #2a2d3e;
        color: white;
    }

    .db-btn-primary {
        background: #22c55e;
        color: white;
    }

    .db-btn-primary:hover {
        background: #1ea951;
    }

    .db-btn-danger {
        background: #ef4444;
        color: white;
    }

    .db-btn-danger:hover {
        background: #dc2626;
    }

    .db-modal-danger-icon {
        width: 64px;
        height: 64px;
        background: rgba(239,68,68,0.1);
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        color: #ef4444;
    }

    .db-modal-text-center {
        text-align: center;
    }

    .db-details-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .db-details-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .db-details-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .db-details-value {
        padding: 12px 16px;
        background: #0f1117;
        border: 1px solid #2a2d3e;
        border-radius: 8px;
        color: #e2e8f0;
        word-break: break-all;
    }

    /* Pagination */
    .pagination-nav button,
    .pagination-nav span {
        cursor: pointer;
    }
</style>
