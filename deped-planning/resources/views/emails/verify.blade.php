<!DOCTYPE html>
<html>
<head>
    <style>
        .button {
            background-color: #003366;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>
<body style="font-family: sans-serif;">
    <h2>Verify Your Email - DepEd Zamboanga</h2>
    <p>Please click the button below to verify your email address:</p>
    
    <p>
        <a href="{{ $url }}" class="button">Verify Email Address</a>
    </p>

    <p>If you did not create an account, no further action is required.</p>
    <hr>
    <p style="font-size: 12px; color: #777;">If the button doesn't work, copy this link: {{ $url }}</p>
</body>
</html>