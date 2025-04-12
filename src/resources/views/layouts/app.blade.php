<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__title">

            </div>
            <nav class="header-nav">
                <ul class="header-nav__list">
                    <li class="header-nav__item"><a href="">ログアウト</a></li>
                    <li class="header-nav__item">マイページ</li>
                </ul>
                <button class="header-nav__button">出品/button>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

</body>

</html>