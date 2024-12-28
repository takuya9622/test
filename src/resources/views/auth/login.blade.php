<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
    <form method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" name="email" placeholder="メールアドレス" required>
    <input type="password" name="password" placeholder="パスワード" required>
    <button type="submit">ログイン</button>
</form>

</body>
</html>