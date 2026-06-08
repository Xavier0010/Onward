<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Onward</title>
    <link rel="icon" type="image/png" href="{{ asset('OnwardLogo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#0d0d0d] text-white overflow-hidden">

    @livewire('user-dashboard')

    <header class="topbar">
    <div class="date-display">
        <span class="cal-icon">📅</span>
        <span id="current-date">Day, Month, Date</span>
    </div>

    <div class="search-area">
        <input 
            type="search" 
            id="search-input" 
            placeholder="Search your task here!" 
            aria-label="Search tasks"
        >

        <img 
            src="{{ asset('assets/onward-logo.png') }}" 
            alt="OnWard Logo" 
            class="logo"
        >
    </div>
</header>

    @livewireScripts
</body>
</html>