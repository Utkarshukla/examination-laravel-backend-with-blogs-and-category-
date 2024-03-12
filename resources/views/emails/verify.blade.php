<!DOCTYPE html>
<html>
<head>
    <title>Verify Email Address</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>
    <p>Please click the following link to verify your email address:</p>
    <p><a href="{{ url('verify-email/'.$user->email.'/'. $user->remember_token) }}">Verify Now</a></p>
</body>
</html>
