<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends - Onward</title>
    <link rel="icon" type="image/png" href="{{ asset('OnwardLogo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#0d0d0d] text-white overflow-hidden">
    @livewire('user-friends')
    @livewireScripts
</body>
</html>
