@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item__content-wrapper">
    <div class="item__img-area">
        <img src=" {{ asset($item -> img_path) }}" class="item__img" alt="商品画像">
    </div>
    <div class="item__content">
        <div class="item__content-header">
            <h2>{{ $item -> name }}</h2>
            <p class="header--brand-name">{{ $item -> brand_name }}</P>
            <p class="header--price">￥<span>{{ number_format($item->price) }}</span>（税込）</p>
            <div class="item__content-button">
                <a href="/purchase/{{ $item->id }}" class="item__content-button--submit">購入手続きへ</a>
            </div>
        </div>

        <div class="item__content-explain">
            <h3>
                商品説明
            </h3>
            <div class="item__content-text">
                {!! nl2br($item -> explain) !!}
            </div>
            <h3>
                商品の情報
            </h3>
            <div class="item__content-status">
                <div class="item__content-status--category">
                    <div class="status__category-label">カテゴリー</div>
                    <div class="status__category-content--tag">
                        @foreach($item->categories as $category)
                        <p class="status__category-content">{{ $category->content }}</p>
                        @endforeach
                    </div>
                </div>
                <div class="item__content-status--condition">
                    <div class="status__condition-label">商品の状態</div>
                    <div class="status__condition-content">{{ $item -> getConditionLabel() }}</div>
                </div>
            </div>
            <div class="item__content-comment">
                <h3 class="item__content-label">
                    コメント
                </h3>
                <div class="item__content-item--comment">
                    コメントの表示スペースです<br>
                    画像、名前、コメント内容が表示されます
                </div>
                <div class="item__content-label">商品へのコメント
                </div>
                <form action="" class="item__comment-form" method="post">
                    @csrf
                    <div class="item__content-item">
                        <textarea name="item__content-textarea"></textarea>
                    </div>
                    <div class="item__content-button">
                        <button class="item__content-button--submit">コメントを送信する</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection