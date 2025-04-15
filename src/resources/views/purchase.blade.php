@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-content">
    <div class="purchase-content__head">
        <div class="purchase-content__head--img">
            <img src="" alt="商品画像">
        </div>
        <div class="purchase-content__head--info">
            <span>商品名</span>
            <p>￥ 金額表示</p>
        </div>
        <div class="purchase-content__payment">
            <p class="purchase-content__label">
                支払方法
            </p>
            <select name="" id="" class="purchase-content__payment-select">
                <option value="">選択してください</option>
                <option value=""></option>
            </select>
        </div>
        <div class="purchase-content__send">
            <p class="purchase-content__label">配送先</p>
            <a href="" class="purchase-content__link">変更する</a>
            <p>〒</p>
            <p>住所表示欄</p>
        </div>
    </div>
    <div class="purchase-content__confirm">
        <div class="confirm__area">
            <form action="" class="confirm-form">
                @csrf
                <label for="" class="confirm-form__label">商品代金</label>
                <input type="text" class="confirm-form__input" readonly value="￥ 47,000">
                <label for="" class="confirm-form__label">支払い方法</label>
                <input type="text" class="confirm-form__input" readonly value="コンビニ払い">
                <div class="confirm-form__button">
                    <button class="confirm-form__button-submit">購入する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection