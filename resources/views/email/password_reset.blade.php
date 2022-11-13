<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Reset Link</title>
</head>
<body>
<strong>Hello!</strong>
<p>You are receiving this email because we received a password reset request for your account.</p>
<a href="{{$reset_link}}">Reset Password</a>
<p>This password reset link will be expired in 60 minutes.</p>
<p>If you did not request a password reset, no further action is required.</p>
<p>Regards,</p>
<p>Laravel</p>
<p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: {{$reset_link}}</p>

</body>
</html>
