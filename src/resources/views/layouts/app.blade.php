<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>COACHTECHフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-container">
            <div class="header-logo">
                <a href="{{ route('items.index') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
                </a>
            </div>

            @if (!in_array(request()->path(), ['login', 'register']))
            <div class="header-search">
                <form class="search-box" action="{{ route('items.index') }}" method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="なにをお探しですか？">
                </form>
            </div>

            <div class="header-nav">
                <ul class="menu-items">
                    @auth
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="logout-form">
                            @csrf
                            <button type="submit" class="header-nav-link">ログアウト</button>
                        </form>
                    </li>
                    @endauth
                    @guest
                    <li><a href="{{ route('login') }}" class="header-nav-link">ログイン</a></li>
                    @endguest
                    <li><a href="{{ route('profile.index') }}">マイページ</a></li>
                    <li><a href="{{ route('items.create') }}" class="post-button">出品</a></li>
                </ul>
            </div>
            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>