@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-content__wrapper">
    <div class="purchase-content">
        <div class="purchase-content__head">
            <div class="purchase-content__head--img">
                <img src="{{ asset($item -> img_path) }}" alt="商品画像">
            </div>
            <div class="purchase-content__head--info">
                <span>{{ $item->name }}</span>
                <p>￥ {{ number_format($item->price) }}</p>
            </div>
        </div>
        <form action="{{ route('purchase.checkout', ['itemId' => $item->id]) }}" method="POST">
            @csrf
            <div class="purchase-content__payment">
                <p class="purchase-content__label">
                    支払方法
                </p>
                <select name="payment" class="purchase-content__payment-select">
                    <option value="" selected>選択してください</option>
                    @foreach ($payments as $key => $payment)
                    <option value="{{ $key }}">{{ $payment }}</option>
                    @endforeach
                </select>
            </div>
            <div class="purchase-content__send">
                <div class="purchase-content__address">
                    <p class="purchase-content__address-label">配送先</p>
                    <p>〒{{ $address['zip_code'] }}</p>
                    <input type="hidden" name="zip_code" value="{{ $address['zip_code'] }}">
                    <p>{{ $address['address'] }}</p>
                    <input type="hidden" name="address" value="{{ $address['address'] }}">
                    <p>{{ $address['building'] }}</p>
                    <input type="hidden" name="building" value="{{ $address['building'] }}">
                </div>
                <div class="purchase-content__change-address">
                    @if( $item->user_id == Auth::id() )
                    @else
                    <a href=" /purchase/address/{{ $item->id }}" class="purchase-content__link">変更する</a>
                    @endif
                </div>
            </div>
    </div>
    <div class="purchase-content__confirm">
        <div class="confirm__area">
            <div class="confirm-form__row">
                <p class="confirm-form__row-title">商品代金</p>
                <p class="confirm-form__row-item">￥ {{ number_format($item->price) }}</p>
            </div>
            <div class=" confirm-form__row">
                <p class="confirm-form__row-title">支払い方法</p>
                <p class="log"></p>
                <script>
                    const element = document.getElementsByName('payment')[0];

                    element.addEventListener('change', handleChange);

                    function handleChange(event) {
                        const selectedOption = element.options[element.selectedIndex];
                        const text = selectedOption.text;
                        document.querySelector(
                            '.log').innerHTML = text;
                    }
                </script>
            </div>
            <div class="confirm-form__button">
                @if( $item->user_id == Auth::id() )
                @else
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                <button class="confirm-form__button-submit">購入する</button>
                @endif
            </div>
            </form>
            @if ($errors->any())
            @foreach ($errors->all() as $error)
            <div class="error">{{ $error }}</div>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection