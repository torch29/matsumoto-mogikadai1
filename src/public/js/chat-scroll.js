document.addEventListener('DOMContentLoaded', function () {
    const chatList = document.getElementById('chat-list');
    if (!chatList) return;

    const editId = chatList.dataset.editId;
    if (!editId) return;

    const target = document.getElementById(`chat-${editId}`);
    if (!target) return;

    // 位置を調整してスクロール
    const offset = target.getBoundingClientRect().top + window.scrollY - 100; // 100px上に余裕
    window.scrollTo({
        top: offset,
        behavior: 'smooth'
    });
});
