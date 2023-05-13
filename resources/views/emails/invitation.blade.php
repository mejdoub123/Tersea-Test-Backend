<head>
    <meta charset="utf-8">
    <title>Invitation Email</title>
</head>

<body>
    <h1>Invitation to Join Our Company</h1>
    <p>Hello,</p>
    <p>You have been invited to join our company. Click the link below to accept the invitation:</p>
    <a href="{{ $data['link'] }}">Accept Invitation</a>
    <p>If the link above doesn't work, you can copy and paste the following URL into your browser:</p>
    <p>{{ $data['link'] }}</p>
    <p>We look forward to having you on board!</p>
</body>

</html>