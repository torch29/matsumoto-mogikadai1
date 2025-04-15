@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item__content-wrapper">
    <div class="item__img">
        <img src="" alt="商品画像">
    </div>
    <div class="item__content">
        <div class="item__content-header">
            <h2>商品名がここに入る</h2>
            <div class="item__content-button">
                <button class="item__content-button--submit">購入手続きへ</button>
            </div>
        </div>

        <div class="item__content-explain">
            <h3>
                商品説明
            </h3>
            <div class="item__content-text">
                商品の説明がここに入ります<br>
                あとで変更されます<br>
                商品の説明欄です<br>
            </div>
            <h3>
                商品の情報
            </h3>
            <div class="item__content-item">
                <div class="item__content-label">カテゴリー</div>
                <div class="item__content-item--category">カテゴリーの表示欄</div>
                <div class="item__content-label">商品の状態</div>
                <div class="item__content-item--condition">商品の状態表示欄</div>
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