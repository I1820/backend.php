<!doctype html>
<html lang="en" xmlns="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{'فراموشی رمز عبور'}}</title>
    <link href="https://cdn.rawgit.com/rastikerdar/vazir-font/v18.0.0/dist/font-face.css" rel="stylesheet"
          type="text/css"/>
    <style>
        .btn:hover {
            background: #3cb0fd linear-gradient(to bottom, #3cb0fd, #3498db);
            text-decoration: none;
        }
    </style>
</head>

<body style="font-family: Vazir, IRANSans, Tahoma, SansSerif,serif;direction: rtl;text-align: right;">
<h3 style="text-align: center;">سامانه اینترنت اشیا دانشکده کامپیوتر دانشگاه صنعتی امیرکبیر</h3>
<div>
    <p>{{$name}} عزیز سلام</p>
    <p>
        این ایمیل برای بازیابی رمز عبور شما ارسال شده است. اگر شما برای این امر تقاضا نداده‌اید می‌توانید
        از محتوای این میل صرف نظر کنید.
    </p>
    <p>
        برای بازیابی رمز عبور خود خود روی دکمه زیر کلیک کنید.
        در صورت کار نکردن دکمه لینک پایین صفحه را کپی کرده در مرورگر خود پیست کنید.
    </p>
    <p>
        توکن شما در یک ساعت آینده منقضی خواهد شد.
    </p>
</div>

<div style="text-align: center;margin-top: 20px;">
    <a class="btn" style="background: #3498db linear-gradient(to bottom, #3498db, #2980b9);
        border-radius: 14px;
        box-shadow: 0 1px 3px #666666;
        color: #ffffff;
        font-size: 18px;
        padding: 10px 20px 10px 20px;
        text-decoration: none;" href="{{$link}}">
        بازیابی رمز عبور
    </a>
</div>
<div style="margin-top: 20px;"><span>{{$link}}</span></div>

</body>
</html>
