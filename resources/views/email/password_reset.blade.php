<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password!!</title>
    <style>
        body {
            margin: 50px;
            background-color: lightgray;
        }

        .main-div {
            width: 1000px;
            background-color: white;
            padding: 50px;
        }

        .link {
            justify-content: center;
            background-color: rgb(95, 95, 227);
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 4px;
            margin: 10px;
        }

        p {
            color: rgb(100, 100, 100);
        }
    </style>
</head>
<body>
<div class="main-div">
    <strong>Hello!</strong>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <a class="link" href="{{$reset_link}}">Reset Password</a>
    <p>This password reset link will be expired in 60 minutes.</p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Regards,</p>
    <p>{{$sender_name}}</p>
    <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <a href="{{$reset_link}}">{{$reset_link}}</a>.</p>
</div>
</body>
</html>
