document.addEventListener('DOMContentLoaded', function () {
    const purchaseId = document.getElementById('purchaseId').value;
    const loginUserId = document.getElementById('loginUserId').value;

    // ユーザーID + 取引ID でキーを作る
    const draftKey = `chat_draft_message_${loginUserId}_${purchaseId}`;

    const chatInput = document.getElementById('chatMessage');

    // 入力時に保存
    chatInput.addEventListener('input', function () {
        localStorage.setItem(draftKey, this.value);
    });

    // 読み込み時に復元
    const savedDraft = localStorage.getItem(draftKey);
    if (savedDraft) {
        chatInput.value = savedDraft;
    }

    // 送信時に削除
    document.querySelector('form').addEventListener('submit', function () {
        localStorage.removeItem(draftKey);
    });
});