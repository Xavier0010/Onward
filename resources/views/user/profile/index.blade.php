<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('OnwardLogo.png') }}">
    <title>Profile - Onward</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#0f1117] text-white antialiased m-0 p-0 overflow-x-hidden">

    @livewire('user-profile', ['userId' => $id ?? null])

    @livewireScripts
</body>
</html>
