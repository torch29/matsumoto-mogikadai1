<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
    <script src="https://kit.fontawesome.com/29c005e54d.js" crossorigin="anonymous"></script>
</head>

<body>
    {{--
    <header class="header">
        <nav class="header-nav">
            <ul class="header-nav__list">
                <li class="header-nav__list-item">
                    <a href="/">
                        <img src="{{ asset('img/logo.svg') }}" class="header__title-img" alt="COACHTECHフリマ">
    </a>
    </li>
    <li class="header-nav__list-item">
        <form action="/" class="search-form" method="get">
            @csrf
            <input type="text" name="search" class="search-form__input" placeholder="なにをお探しですか？" value="{{ isset($word) ? $word : '' }}">
            <button class="search-form__button--hidden">検索</button>
        </form>
    </li>
    <li class="header-nav__list-item">
        <ul class="header-nav__list--user">
            @if (Auth::check())
            <li class="header-nav__user-item">
                <form action="/logout" class="header-nav__form" method="post">
                    @csrf
                    <button class="header-nav__button-submit">ログアウト</button>
                </form>
            </li>
            @else
            <li class="header-nav__user-item">
                <form action="/login" method="get">
                    @csrf
                    <button class="header-nav__button-submit">ログイン</button>
                </form>
            </li>
            @endif
            <li class="header-nav__user-item"><a href="/mypage">マイページ</a></li>
            <a href="/sell" class="header-nav__button">出品</a>
        </ul>
    </li>
    </ul>
    </nav>
    </header>
    --}}

    <header class="header">
        <nav class="header-nav">
            <h1 class="header-nav__ttl">
                <a href="/">
                    <img src="{{ asset('img/logo.svg') }}" class="header__title-img" alt="COACHTECHフリマ">
                </a>
            </h1>
            <ul class="header-nav__list">
                <li class="header-nav__list-item">
                    <form action="/" class="search-form" method="get">
                        @csrf
                        <input type="text" name="search" class="search-form__input" placeholder="なにをお探しですか？" value="{{ isset($word) ? $word : '' }}">
                        <button class="search-form__button--hidden">検索</button>
                    </form>
                </li>
                <li class="header-nav__list-item">
                    <ul class="header-nav__list--user">
                        @if (Auth::check())
                        <li class="header-nav__user-item">
                            <form action="/logout" class="header-nav__form" method="post">
                                @csrf
                                <button class="header-nav__button-submit">ログアウト</button>
                            </form>
                        </li>
                        @else
                        <li class="header-nav__user-item">
                            <form action="/login" method="get">
                                @csrf
                                <button class="header-nav__button-submit">ログイン</button>
                            </form>
                        </li>
                        @endif
                        <li class="header-nav__user-item"><a href="/mypage">マイページ</a></li>
                        <a href="/sell" class="header-nav__button">出品</a>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

</body>

</html>