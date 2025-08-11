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
        <div class="message__block {{ $chat->sender_id == auth()->id() ? 'right' : '' }}">
            <div class="message__block--inner">
                <div class="message-header">
                    <div class="message-header__icon">
                        {{-- プロフィール画像表示 / 頭文字の表示欄 --}}
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
                {{-- メッセージ / 編集フォーム --}}
                @if (request('edit') == $chat->id)
                <div class="message {{ $chat->sender_id == auth()->id() ? 'right' : '' }}">
                    <form action="/mypage/chat/update" method="POST">
                        @method('PATCH')
                        @csrf
                        <input type="hidden" name="id" value="{{ $chat->id }}">
                        <input type="hidden" name="transitionId" value="{{ $tradingItem->purchasedItem->id }}">
                        <input type="text" name="message" value="{{ $chat->message }}">
                        <button>送信</button>
                        <a href="/mypage/chat/{{$tradingItem->purchasedItem->id}}">
                            <span>編集せず終了</span>
                        </a>
                    </form>
                </div>
                @else
                <div class="message {{ $chat->sender_id == auth()->id() ? 'right' : '' }}">
                    {{ $chat->message }}
                    @if( $chat->img_path )
                    <div class="message__img">
                        <img src="{{ asset($chat->img_path) }}" alt="">
                    </div>
                    @endif
                </div>
                {{-- 編集・削除機能（自分のメッセージのみ） --}}
                @if($chat->sender_id == auth()->id())
                <div class="message__option">
                    <a href="{{ url()->current() }}?edit={{ $chat->id }}">
                        <span>編集</span>
                    </a>
                    <form action="/mypage/chat/delete" method="post">
                        @method('DELETE')
                        @csrf
                        <button>
                            <input type="hidden" name="id" value="{{ $chat->id }}">
                            <input type="hidden" name="transitionId" value="{{ $tradingItem->purchasedItem->id }}">
                            <span>削除</span>
                        </button>
                    </form>
                </div>
                @endif
                @endif
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
            <input type="text" name="message" id="chatMessage" class="chat__input" placeholder="取引メッセージを記入してください">
            <label for="img_path" class="sell-form__img-button--label">
                画像を選択する
                <input type="file" name="img_path" id="img_path" class="sell-form__img-button">
            </label>
            <div class="chat__actions">
                <button type="submit">
                    <input type="hidden" name="purchase_id" id="purchaseId" value="{{ $tradingItem->id }}">
                    <input type="hidden" id="loginUserId" value="{{ auth()->id() }}">
                    <img src="" alt="送信">
                </button>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/save_input.js') }}"></script>

{{--　あとでけす
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const purchaseId = @json($tradingItem -> id);
        const draftKey = 'chat_draft_message_' + purchaseId;
        const input = document.getElementById('chatMessage');

        // 復元
        const savedDraft = localStorage.getItem(draftKey);
        if (savedDraft) {
            input.value = savedDraft;
        }

        // 入力時に保存
        input.addEventListener('input', function() {
            localStorage.setItem(draftKey, this.value);
        });

        // 送信時に削除
        input.form.addEventListener('submit', function() {
            localStorage.removeItem(draftKey);
        });
    });
</script>
--}}

@endsection