<!DOCTYPE html>
<html>
<head>
    <title>Verify Email Address</title>
</head>
<body>
    @php
        $url= config('services.frontend_url.frontend_url_r');
    @endphp
    <p>Hello {{ $user->name }},</p>
    <p>"We have received your information. 
        You are eligible for the position of an incharge at our school. 
        To proceed with your application, please watch the instructional 
        video provided at the link below." </p>

    <a href="{{$url}}/demo/video">Take A Demo</a>
    
    <p><a href="{{$url}}/login/">Login</a></p>
</body>
</html>
