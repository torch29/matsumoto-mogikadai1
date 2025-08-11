@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

{{-- 購入者用取引チャット画面 --}}
@section('content')
<div class="chat">
    {{-- サイドナビ --}}
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
    {{-- チャット用メイン画面 --}}
    <div class="chat__heading">
        {{-- タイトル欄 --}}
        <div class="heading__title">
            <div class="heading__info--user">
                <div class="heading__icon">
                    @if( $tradingItem->purchasedItem->users->profile->profile_img )
                    <img src="{{ asset($tradingItem->purchasedItem->users->profile->profile_img) }}" alt="">
                    @else
                    <div class="heading__icon--name">
                        {{ mb_substr($tradingItem->purchasedItem->users->name, 0 ,1) }}
                    </div>
                    @endif
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
    {{-- 商品情報 --}}
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
    {{-- チャット画面 --}}
    <div class="chat__content">
        {{ dump($tradingItem->id) }}
        @foreach( $chats as $chat )
        <div class="message__block">
            <div class="message-header">
                <div class="message-header__icon">
                    @if( $chat->sendUser->profile->profile_img )
                    <img src="{{ asset($chat->sendUser->profile->profile_img) }}" alt="">
                    @else
                    <div class="icon--name">
                        {{ mb_substr($chat->sendUser->name, 0 ,1) }}
                    </div>
                    @endif
                </div>
                <div class="message-header__name">
                    {{ $chat->sendUser->name }}
                </div>
            </div>
            <div class="message">
                {{ $chat->message }}
                {{ dump($chat->img_path) }}
                <div class="message__img">
                    <img src="{{ asset($chat->img_path) }}" alt="">
                </div>
            </div>
        </div>
        @endforeach
        購入者（自分）：{{ $tradingItem->purchasedUser->name }}
        出品者（相手）：{{ $tradingItem->purchasedItem->users->name
                        }}
    </div>
    {{-- チャットメッセージ送信蘭 --}}
    <div class="chat__footer">
        <form action="/mypage/chat" method="post" class="chat-form" enctype="multipart/form-data">
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