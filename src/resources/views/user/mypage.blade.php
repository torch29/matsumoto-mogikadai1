@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-header">
    <div class="mypage-header__heading">
        <div class="mypage-header__heading-icon">
            <img src="{{ asset( $user->profile->profile_img ) }}" alt="ユーザーアイコン">
        </div>
        <div class="mypage-header__heading-name">
            {{ Auth::user()->name }}
        </div>
    </div>
    <div class="mypage-header__button">
        <a href="/mypage/profile" class="mypage-header__button-submit">プロフィールを編集</a>
    </div>
</div>

<nav class="mypage-nav">
    <ul class="mypage-nav__list">
        <li class="mypage-nav__list-item"><a href="">出品した商品</a></li>
        <li class="mypage-nav__list-item"><a href="">購入した商品</a></li>
    </ul>
</nav>
<div class="mypage-content">
    <div class="item-card__container">
        <ul class="item-card__content">
            @foreach ( $sellItems as $sellItem)
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    @if($sellItem->status == 'available')
                    <a href="/item/{{ $sellItem->id }}">
                        <img src="{{ $sellItem->img_path }}" class="item-card__content--img" alt="商品画像">
                        <p>{{ $sellItem->name }}</p>
                    </a>
                    @else
                    <a href="/item/{{ $sellItem->id }}">
                        <img src="{{ $sellItem->img_path }}" class="item-card__content--sold-img" alt="商品画像">
                        <div class="item-sold">sold</div>
                        <p>{{ $sellItem->name }}</p>
                    </a>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection