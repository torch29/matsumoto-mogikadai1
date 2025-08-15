<div id="chat-list" data-edit-id="{{ request('edit') }}">
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
            <div id="chat-{{ $chat->id }}" class="message {{ $chat->sender_id == auth()->id() ? 'right' : '' }}">
                <div class="message__edit">
                    <form action="/mypage/chat/update" method="POST">
                        @method('PATCH')
                        @csrf
                        <input type="hidden" name="id" value="{{ $chat->id }}">
                        <input type="hidden" name="transitionId" value="{{ $tradingItem->purchasedItem->id }}">
                        <textarea name="message" class="message__input--edit">{{ $chat->message }}</textarea>
                        <div class="message__actions--edit">
                            <button class="message__button--edit">送信</button>
                            <a href="/mypage/chat/{{$tradingItem->purchasedItem->id}}#chat-{{ $chat->id }}">
                                <span class="message__link--edit-end">編集取消</span>
                            </a>
                        </div>
                    </form>
                </div>
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
                <a href="{{ url()->current() }}?edit={{ $chat->id }}#chat-{{ $chat->id }}">
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
</div>