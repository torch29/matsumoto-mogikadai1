@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-header">
    <div class="mypage-header__heading">
        <div class="mypage-header__heading-icon">
            <img src="{{ asset( $user->profile->profile_img) }}">
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
</div>
<div class="mypage-content">
    {{-- ここから出品した商品の一覧 --}}
    <div class="tab-panel" id="sellItems">
        出品した商品
        <ul class="item-card__content">
            @foreach ( $sellItems as $sellItem)
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    @if($sellItem->status == 'available')
                    <a href="/item/{{ $sellItem->id }}">
                        <img src="{{ $sellItem->img_path }}" class="item-card__content--img" alt="商品画像">
                        <p>{{ $sellItem->name }}</p>
                    </a>
                    @else
                    <a href="/item/{{ $sellItem->id }}">
                        <img src="{{ $sellItem->img_path }}" class="item-card__content--sold-img" alt="商品画像">
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
        購入した商品
        <ul class="item-card__content">
            @foreach ($purchasedItems as $purchasedItem)
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    @if($purchasedItem->purchasedItem->status == 'available')
                    <a href="/item/{{ $purchasedItem->id }}">
                        <img src="{{ $purchasedItem->purchasedItem->img_path }}" class="item-card__content--img" alt="商品画像">
                        <p>{{ $purchasedItem->purchasedItem->name }}</p>
                    </a>
                    @else
                    <a href="/item/{{ $purchasedItem->purchasedItem->id }}">
                        <img src="{{ $purchasedItem->purchasedItem->img_path }}" class="item-card__content--sold-img" alt="商品画像">
                        <div class="item-sold">sold</div>
                        <p>{{ $purchasedItem->purchasedItem->name }}</p>
                    </a>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
<script>
    const buttons = document.querySelectorAll('.mypage-tab__button');
    const contents = document.querySelectorAll('.tab-panel');

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