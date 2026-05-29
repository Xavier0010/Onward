<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<nav>
    <a href="/admin/statistics">Statistics</a>
    
    @foreach ($tables as $t)
        <a href="/admin/{{ $t }}">{{ $t }}</a>
    @endforeach
</nav>

<hr>

@yield('content')

</body>
</html>