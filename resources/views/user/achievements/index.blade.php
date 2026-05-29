<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - Onward</title>
    <link rel="icon" type="image/png" href="{{ asset('OnwardLogo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-[#0d0d0d] text-white overflow-hidden">

    @livewire('user-achievement')

    @livewireScripts
    @stack('scripts')
</body>
</html>