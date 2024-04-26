<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Ticket</title>
</head>
<body>
    <h1>Hall Ticket</h1>
    <p>Hello {{ $user->participantUser->name }},</p>
    <p>Your hall ticket for the Matrix Olympiads is:</p>
    <p><strong>{{ $user->hall_ticket_no }}</strong></p>
    <p>Please bring this hall ticket with you to the Olympiad.</p>
    <p>Thank you!</p>
</body>
</html>
