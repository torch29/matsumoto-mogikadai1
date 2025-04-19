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
        <nav class="header-nav">
            <a href="/">
                <img src="{{ asset('img/logo.svg') }}" class="header__title-img" alt="COACHTECHフリマ">
            </a>
            <form action="" class="search-form">
                @csrf
                <input type="text" class="search-form__input" placeholder="なにをお探しですか？">
            </form>
            <ul class="header-nav__list">
                <form action="/logout" class="header-nav__form" method="post">
                    @csrf
                    @if (Auth::check())
                    <li class="header-nav__item"><a class="header-nav__button--logout">ログアウト</a></li>
                    @else
                    <li class="header-nav__item"><a class="header-nav__button--logout">ログイン</a></li>
                    @endif
                </form>
                <li class="header-nav__item"><a href="/mypage">マイページ</a></li>
                <a href="/sell" class="header-nav__button">出品</a>
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

</body>

</html>