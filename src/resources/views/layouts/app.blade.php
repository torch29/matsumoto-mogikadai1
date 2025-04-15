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
            <img src="{{ asset('img/logo.svg') }}" class="header__title-img" alt="COACHTECHフリマ">
            @if (Auth::check())
            <form action="" class="search-form">
                @csrf
                <input type="text" class="search-form__input" placeholder="なにをお探しですか？">
            </form>
            <ul class="header-nav__list">
                <form action="/logout" class="header-nav__form" method="post">
                    @csrf
                    <li class="header-nav__item"><button class="header-nav__button--logout">ログアウト</button></li>
                </form>
                <li class="header-nav__item"><a href="">マイページ</a></li>
                <button class="header-nav__button">出品</button>
            </ul>
            @endif
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

</body>

</html>