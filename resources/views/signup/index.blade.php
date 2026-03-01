<!DOCTYPE html>
<html>
<head>
    <title>Signup Test</title>
</head>
<body>

<h2>Signup</h2>

@if ($errors->any())
    <div>
        @foreach ($errors->all() as $error)
            <p style="color:red">{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="/api/auth/signup">
    @csrf

    <input name="username" placeholder="Username"><br><br>

    <input name="email" placeholder="Email"><br><br>

    <input type="password" name="password" placeholder="Password"><br><br>

    <input type="password" name="password_confirmation" placeholder="Confirm Password"><br><br>

    <input name="first_name" placeholder="First Name"><br><br>

    <input name="last_name" placeholder="Last Name"><br><br>

    <select name="sex">
        <option value="">Select Sex</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
    </select><br><br>

    <input type="date" name="date_of_birth"><br><br>

    <button type="submit">Signup</button>
</form>

</body>
</html>
