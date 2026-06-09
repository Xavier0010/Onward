<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Onward</title>
    <link rel="icon" type="image/png" href="{{ asset('OnwardLogo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f1117;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col relative text-white">
    <!-- Header with Logo -->
    <header class="p-6 absolute top-0 left-0 w-full flex items-center justify-between z-50">
        <div class="flex items-center gap-2">
            <a href="/"><img src="{{ asset('OnwardLogo.png') }}" alt="Onward Logo" class="h-20 w-auto object-contain"></a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center p-4 pt-24 pb-12">
        @livewire('login')
    </main>

    @livewireScripts
</body>
</html>
