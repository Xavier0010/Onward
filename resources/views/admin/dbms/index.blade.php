<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('OnwardLogo.png') }}">
    <title>Admin DBMS - Onward</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#0d0d0d] text-white overflow-hidden">
    @livewire('admin_dbms')
    @livewireScripts
</body>
</html>
