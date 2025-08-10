@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

{{-- 購入者用取引チャット画面 --}}
@section('content')
<div class="chat">
    <div class="chat-nav--side">
        <div class="chat-nav__title">
            <h3>その他の取引</h3>
        </div>
        <div class="chat-nav__list">
            @foreach ( $tradingItemList as $tradingItemRecord )
            <div class="chat-nav__list--title">
                <a href="/mypage/chat/{{ $tradingItemRecord->purchasedItem->id }}">
                    {{ $tradingItemRecord->purchasedItem->name }}
                </a>
            </div>
            @endforeach
        </div>
    </div>
    <div class="chat__heading">
        <div class="heading__title">
            <div class="heading__info--user">
                <div class="heading__icon">
                    <img src="" alt="">
                </div>
                <h2>{{ $tradingItem->purchasedItem->users->name }}さんとの取引画面</h2>
            </div>
            <div class="heading__button">
                <button>
                    取引を完了する
                </button>
            </div>
        </div>
    </div>
    <div class="chat__info--area">
        <div class="info__img">
            <img src="{{ asset($tradingItem->purchasedItem->img_path) }}" alt="商品画像">
        </div>
        <div class="info__item--lead">
            <div class="info__item--name">
                {{ $tradingItem->purchasedItem->name }}
            </div>
            <div class="info__item--price">
                ￥{{ number_format($tradingItem->purchasedItem->price) }}
            </div>
        </div>
    </div>
    <div class="chat__content">
        {{ dump($tradingItem->id) }}
        @foreach( $chats as $chat )
        <div class="message__block">
            <div class="message-header">
                <div class="message-header__icon"></div>
                <div class="message-header__name">
                    {{ $chat->tradingPurchaseItem->purchasedUser->name }}
                </div>
            </div>
            <div class="message">
                {{ $chat->message }}
            </div>
        </div>
        @endforeach
        購入者（自分）：{{ $tradingItem->purchasedUser->name }}
        出品者（相手）：{{ $tradingItem->purchasedItem->users->name
                        }}
    </div>
    <div class="chat__footer">
        <form action="/mypage/chat" method="post" class="chat-form">
            @csrf
            <input type="text" name="message" class="chat__input" placeholder="取引メッセージを記入してください">
            <label for="img_path" class="sell-form__img-button--label">
                画像を選択する
                <input type="file" name="img_path" id="img_path" class="sell-form__img-button">
            </label>
            <div class="chat__actions">
                <button type="submit">
                    <input type="hidden" name="purchase_id" value="{{ $tradingItem->id }}">
                    <img src="" alt="送信">
                </button>
            </div>
        </form>
    </div>
</div>
@endsection