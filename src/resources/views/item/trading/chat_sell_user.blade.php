@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

{{-- 出品者用取引チャット画面 --}}
@section('content')
<div class="chat">
    {{-- サイドナビ --}}
    <div class="chat-nav--side">
        <div class="chat-nav__title">
            <h3>その他の取引</h3>
        </div>
        <div class="chat-nav__list">
            @foreach ( $tradingItems as $tradingItemRecord )
            <div class="chat-nav__list--title {{ $tradingItemRecord->id == $currentItemId ? 'current' : '' }}">
                <a href="/mypage/chat/{{ $tradingItemRecord->id }}">
                    {{ $tradingItemRecord->name }}
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
                    @if( $tradingItem->purchasedUser->profile->profile_img )
                    <img src="{{ asset($tradingItem->purchasedUser->profile->profile_img) }}" alt="">
                    @else
                    <div class="icon--name">
                        {{ mb_substr($tradingItem->purchasedUser->name, 0 ,1) }}
                    </div>
                    @endif
                </div>
                <h2>
                    {{-- 購入者名が入る --}}
                    {{ $tradingItem->purchasedUser->name }}さんとの取引画面
                </h2>
            </div>
            <div class="heading__button--wrapper">
                @error('alert')
                <div class="error__message top">
                    <span>{{ $message }}</span>
                </div>
                @enderror
                {{-- 取引評価用モーダル --}}
                @if( $tradingItem->status === 'buyer_rated' )
                <div class="heading__modal">
                    <button popovertarget="mypopover" class="modal__button--open">
                        取引を完了する
                    </button>
                    <div id="mypopover" popover class="modal__window">
                        <div class="modal__title">
                            取引が完了しました。
                        </div>
                        <div class="modal__content">
                            <span>今回の取引相手はどうでしたか？</span>
                            <form action="{{ route('seller.rating') }}" class="rating-form" method="POST">
                                @csrf
                                <div class="rating-form__inner">
                                    @for($i=5; $i>0; $i--)
                                    <input type="radio" id="star{{$i}}" name="score" value="{{$i}}" class="rating-form__input">
                                    <label for="star{{$i}}" class="rating-form__label"><i class="fa-solid fa-star"></i></label>
                                    @endfor
                                </div>

                                <div class="modal__action">
                                    <input type="hidden" name="reviewee_id" value="{{ $tradingItem->purchasedUser->id }}">
                                    <input type="hidden" value="{{  $tradingItem->id }}" name="purchase_id">
                                    <button type="submit" class="modal__button--submit">送信する</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
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

        @include('item.trading._chat_messages', ['chats' => $chats])

    </div>
    @if ( $tradingItem->status === 'trading' )
    {{-- チャットメッセージ送信蘭 --}}
    <div class="chat__footer">
        <div class="error__message">
            @error('message')
            <span>{{ $message }}</span>
            @enderror
            @error('img_path')
            <span>{{ $message }}</span>
            @enderror
        </div>

        @include('item.trading._chat_form', ['tradingItem' => $tradingItem])

        @elseif ( $tradingItem->status === 'buyer_rated' )
        <p>{{ $tradingItem->purchasedUser->name }}さんがこの取引を完了しました。
            右上のボタンから取引を完了させてください。</p>
        @elseif ( $tradingItem->status === 'completed' )
        <p>この取引は既に完了しています。</p>
        @endif
    </div>
</div>
<script src="{{ asset('js/save_input.js') }}"></script>
<script src="{{ asset('js/file_name_display.js') }}"></script>
<script src="{{ asset('js/chat-scroll.js') }}"></script>

@endsection