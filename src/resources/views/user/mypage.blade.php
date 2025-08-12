@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-header">
    <div class="mypage-header__heading">
        <div class="mypage-header__heading-icon">
            @if(optional($user->profile)->profile_img)
            <img src="{{ asset( $user->profile->profile_img) }}" alt="">
            @else
            <div class="mypage__icon-name">{{ mb_substr(Auth::user()->name, 0, 1 ) }}</div>
            @endif
        </div>
        <div class="mypage-header__heading-name">
            {{ Auth::user()->name }}
        </div>
    </div>
    <div class="mypage-header__button">
        <a href="/mypage/profile" class="mypage-header__button-submit">プロフィールを編集</a>
    </div>
</div>

<div class="mypage-tab__list">
    <button class="mypage-tab__button active" data-tab="sellItems">出品した商品</button>
    <button class="mypage-tab__button" data-tab="purchasedItems">購入した商品</button>
    <button class="mypage-tab__button" data-tab="tradingItems">取引中の商品</button>
</div>
<div class="mypage-content">
    {{-- ここから出品した商品の一覧 --}}
    <div class="tab-panel" id="sellItems">
        <ul class="item-card__content">
            @foreach ( $sellItems as $sellItem)
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    @if($sellItem->status == 'available')
                    <a href="/item/{{ $sellItem->id }}">
                        <img src="{{ asset($sellItem->img_path) }}" class="item-card__content--img" alt="商品画像">
                        <p>{{ $sellItem->name }}</p>
                    </a>
                    @else
                    <a href="/item/{{ $sellItem->id }}">
                        <img src="{{ asset($sellItem->img_path) }}" class="item-card__content--sold-img" alt="商品画像">
                        <div class="item-sold">sold</div>
                        <p>{{ $sellItem->name }}</p>
                    </a>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    {{-- ここから購入した商品の一覧 --}}
    <div class="tab-panel" id="purchasedItems" style="display: none;">
        <ul class="item-card__content">
            @forelse ($purchasedItems as $purchasedItem)
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    <a href="/item/{{ $purchasedItem->purchasedItem->id }}">
                        <img src="{{ asset($purchasedItem->purchasedItem->img_path) }}" class="item-card__content--img" alt="商品画像">
                        <div class="item-purchasedItem"><span>購入しました</span></div>
                        <p>{{ $purchasedItem->purchasedItem->name }}</p>
                    </a>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    {{-- ここから取引中の商品一覧 --}}
    <div class="tab-panel" id="tradingItems" style="display: none;">
        <ul class="item-card__content">
            @foreach ( $tradingItems as $tradingItem)
            @php
            $purchaseId = $tradingItem->purchases->first()->id ?? null;
            $unreadCount = $unreadCounts[$purchaseId] ?? 0;
            @endphp
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    @if($tradingItem->purchases->first()->status == 'trading')
                    <a href="/mypage/chat/{{ $tradingItem->id }}">
                        <img src="{{ asset($tradingItem->img_path) }}" class="item-card__content--img" alt="商品画像">
                        @if ($unreadCount > 0)
                        <span class="notify-badge">{{ $unreadCount }}</span>
                        @endif
                        <p>{{ $tradingItem->name }}</p>
                    </a>
                    @endif
                </div>
            </li>
            @empty
            <p>取引中の商品がある場合ここに表示されます。</p>
            @endforelse
        </ul>
    </div>
    {{-- タブここまで --}}
</div>
<script>
    const konbiniCheckoutUrl = @js($konbiniCheckoutUrl);
    if (konbiniCheckoutUrl) {
        window.open(konbiniCheckoutUrl, '_blank');
    }
    // URLのパラメータを取得
    const urlParams = new URLSearchParams(window.location.search);
    // デフォルトはsellタブ
    const tab = urlParams.get('tab') || 'sell';

    // タブのボタンとコンテンツを取得
    const buttons = document.querySelectorAll('.mypage-tab__button');
    const contents = document.querySelectorAll('.tab-panel');

    // ページが読み込まれた時に、対応するタブを表示
    window.addEventListener('DOMContentLoaded', function() {
        // 最初に、全てのactiveを外す＆全てのタブを非表示にする
        buttons.forEach(btn => btn.classList.remove('active'));
        contents.forEach(content => content.style.display = 'none');
        // デフォルトのタブを設定
        let defaultTab = tab === 'buy' ? 'purchasedItems' : 'sellItems';

        // ボタンの「active」クラスを切り替え
        const targetButton = document.querySelector(`.mypage-tab__button[data-tab="${defaultTab}"]`);
        if (targetButton) {
            targetButton.classList.add('active');
        }

        // タブ内容を表示
        const targetContent = document.getElementById(defaultTab);
        if (targetContent) {
            targetContent.style.display = 'block';
        }
    });

    // タブクリック時の動作
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.getAttribute('data-tab');

            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            contents.forEach(content => {
                if (content.id === tab) {
                    content.style.display = 'block';
                } else {
                    content.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection