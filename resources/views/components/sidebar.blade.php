@php
    $current = request()->path();
@endphp

@if (Auth::user()->role === 'user') 
    <aside class="db-sidebar-nav">
        <div class="db-sidebar-top">
            <a href="/"><img src="{{ asset('OnwardLogo.png') }}" alt="Onward Logo" class="db-logo"></a>
        </div>
        <div class="db-sidebar-middle">
            <!-- Dashboard Icon -->
            <a href="/user/dashboard" class="db-nav-item {{ $active === 'dashboard' ? 'active' : '' }}" title="Dashboard">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
            </a>
            <!-- Achievement Icon -->
            <a href="/user/achievements" class="db-nav-item {{ $active === 'achievements' ? 'active' : '' }}" title="Achievements">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12v5l10 5 10-5v-5"/></svg>
            </a>
            <!-- Leaderboard Icon -->
            <a href="/user/leaderboard" class="db-nav-item {{ $active === 'leaderboard' ? 'active' : '' }}" title="Leaderboard">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75,7 20.24,7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75,17 20.24,17 22"></path><path d="M18 2H6v7a6 6 0 0 1-12 0V2z"></path></svg>
            </a>
            <!-- Store Icon -->
            <a href="/user/store" class="db-nav-item {{ $active === 'store' ? 'active' : '' }}" title="Store">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.58-4.58a6 6 0 0 1 8.48 0L19 5"/><path d="M3.5 14h13a3 3 0 0 1 2.97 3.52l.28 2.24a1 1 0 0 1-.99 1.14H4.24a1 1 0 0 1-1-1.14l.28-2.24A3 3 0 0 1 3.5 14Z"/><path d="M4.5 14.5 4 20"/><path d="M15.5 14.5 16 20"/><circle cx="9" cy="10" r="1"/><circle cx="14" cy="10" r="1"/></svg>
            </a>
            <!-- Friends Icon -->
            <a href="/user/friends" class="db-nav-item {{ $active === 'friends' ? 'active' : '' }}" title="Friends">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </a>
        </div>
        <div class="db-sidebar-bottom">
            <!-- Profile Icon -->
            <a href="/user/profile" class="db-nav-item {{ $active === 'profile' ? 'active' : '' }}" title="Profile">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </a>
            <!-- Logout Icon -->
            <a
                href="/logout"
                class="db-nav-item"
                title="Logout"
            >
                <svg width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </a>
        </div>
    </aside>

@else
    <aside class="db-sidebar-nav">
        <div class="db-sidebar-top">
            <a href="/"><img src="{{ asset('OnwardLogo.png') }}" alt="Onward Logo" class="db-logo"></a>
        </div>
        <div class="db-sidebar-middle">
            <!-- Dashboard Icon -->
            <a href="/admin/dashboard" class="db-nav-item {{ $active === 'dashboard' ? 'active' : '' }}" title="Dashboard">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
            </a>
            <!-- DBMS Icon -->
            <a href="/admin/dbms" class="db-nav-item {{ $active === 'dbms' ? 'active' : '' }}" title="DBMS">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12v5l10 5 10-5v-5"/></svg>
            </a>
        </div>
        <div class="db-sidebar-bottom">
            <!-- Profile Icon -->
            <a href="/user/profile" class="db-nav-item {{ $active === 'profile' ? 'active' : '' }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </a>
            <!-- Logout Icon -->
            <a
                href="/logout"
                class="db-nav-item"
                title="Logout"
            >
                <svg width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </a>
        </div>
    </aside>
@endif
