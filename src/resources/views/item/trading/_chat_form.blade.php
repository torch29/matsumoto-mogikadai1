<form action="/mypage/chat" method="post" class="chat-form" enctype="multipart/form-data">
    @csrf
    <input type="text" name="message" id="chatMessage" class="chat__input" placeholder="取引メッセージを記入してください">
    <label for="img_path" class="chat-form__img-button--label">
        画像を選択する
        <input type="file" name="img_path" id="img_path" class="chat-form__img-button">
        <span id="selectedFileName" class="chat-form__filename"></span>
    </label>
    <input type="hidden" name="purchase_id" id="purchaseId" value="{{ $tradingItem->id }}">
    <input type="hidden" id="loginUserId" value="{{ auth()->id() }}">
    <div class="chat__actions">
        <button type="submit" class="chat__button-submit">
            <img src="{{ asset('img/preset/icon/send.jpg') }}" class="" alt="送信">
        </button>
    </div>
</form>