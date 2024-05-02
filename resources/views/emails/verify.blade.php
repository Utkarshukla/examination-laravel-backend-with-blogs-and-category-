<!DOCTYPE html>
<html>
<head>
    <title>Verify Email Address</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>
    <p>Please click the following link to verify your email address:</p>
    @php
        $url= config('services.frontend_url.frontend_url_r');
    @endphp
    <p><a href="{{$url}}/verify-token/?email={{ $user->email }}&token-id={{ $user->remember_token }}">Verify Now</a></p>
</body>
</html>
