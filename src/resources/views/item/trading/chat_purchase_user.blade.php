@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat">
    <div class="chat-nav--side">
        <div class="chat-nav__title">
            <p>その他の取引</p>
        </div>
        <div class="chat-nav__list">
            商品名
        </div>
    </div>
    <div class="chat__main">
        <div class="chat__heading">
            <div class="heading__title">
                <div class="heading__info--user">
                    <div class="heading__icon">
                        <img src="" alt="">
                    </div>
                    <p>ユーザー名さんとの取引画面</p>
                </div>
                <div class="heading__button">
                    <button>
                        取引を完了する
                    </button>
                </div>
            </div>
        </div>
        <div class="chat__info--item">
            <div class="info__img">
                <img src="" alt="">
            </div>
            <div class="info__title--item">
                商品名
                商品価格
            </div>
        </div>
        <div class="chat__content">
            <div class="message__block">
                <div class="message-header">
                    <div class="message-header__icon"></div>
                    <div class="message-header__name">ユーザー名</div>
                </div>
                <div class="message">
                    メッセージが表示される
                </div>
            </div>
        </div>
    </div>
</div>
@endsection