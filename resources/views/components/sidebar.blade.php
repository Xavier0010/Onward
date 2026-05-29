    <aside class="db-sidebar-nav">
        <div class="db-sidebar-top">
            <img src="{{ asset('OnwardLogo.png') }}" alt="Onward Logo" class="db-logo">
        </div>
        <div class="db-sidebar-middle">
            <!-- Dashboard Icon -->
            <a href="/user/dashboard" class="db-nav-item {{ Request::is('user/dashboard') ? 'active' : '' }}" title="Dashboard">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
            </a>
            <!-- Achievement Icon -->
            <a href="/user/achievements" class="db-nav-item {{ Request::is('user/achievements') ? 'active' : '' }}" title="Achievement">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12v5l10 5 10-5v-5"/></svg>
            </a>
            <!-- Leaderboard Icon -->
            <a href="#" class="db-nav-item {{ Request::is('user/leaderboard') ? 'active' : '' }}" title="Leaderboard">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 1-12 0V2z"></path></svg>
            </a>
        </div>
        <div class="db-sidebar-bottom">
            <!-- Profile Icon -->
            <a href="/user/profile" class="db-nav-item {{ Request::is('user/profile') ? 'active' : '' }}" title="Profile">
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