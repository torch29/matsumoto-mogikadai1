@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<nav class="main-nav">
    <ul class="main-nav__list">
        <li class="main-nav__list-item">おすすめ</li>
        <li class="main-nav__list-item"><a href="">マイリスト</a></li>
    </ul>
</nav>
<div class="main-content">
    <div class="item-card__container">
        <ul class="item-card__content">
            <li class="item-card__content--list">
                <div class="item-card__content--img">
                    <img src="" alt="商品画像">
                </div>
                <p>商品名</p>
            </li>
            {{-- あとでforeachにする予定、以下消す --}}
            <li class="item-card__content--list">
                <div class="item-card__content--img">
                    <img src="" alt="商品画像">
                </div>
                <p>テスト用リスト</p>
            </li>
            <li class="item-card__content--list">
                <div class="item-card__content--img">
                    <img src="" alt="商品画像">
                </div>
                <p>テスト用リスト</p>
            </li>
        </ul>
    </div>
</div>
@endsection