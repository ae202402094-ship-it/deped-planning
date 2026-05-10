<!DOCTYPE html>
<html>
<head>
    <style>
        .wrapper { background-color: #f8fafc; padding: 20px; font-family: sans-serif; }
        .content { background: white; padding: 40px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="content">
            {{ $slot }}
        </div>
    </div>
</body>
</html>