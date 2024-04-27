<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
</head>
<body>
    <h1>Certificate from matrix olympiad</h1>
    <p>Hello {{ $user->participantUser->name }},</p>
    <p>Your hall ticket for the Matrix Olympiads is:</p>
    <p><a href="{{ $user->certificate_url }}"><button style="padding:10px 20px 10px 20px;">Certificate</button></a></p>
    <p>Best of luck.</p>
    <p>Thank you!</p>
</body>
</html>
