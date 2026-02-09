<!DOCTYPE html>
<html>
<head>
    <title>Login Test</title>
</head>
<body>

<h2>Login</h2>

@if ($errors->any())
    <div>
        @foreach ($errors->all() as $error)
            <p style="color:red">{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<form method="POST">
    @csrf

    <input name="login" placeholder="Email or Username"><br><br>

    <input type="password" name="password" placeholder="Password"><br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
