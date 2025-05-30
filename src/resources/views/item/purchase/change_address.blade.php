@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection

@section('content')
<div class="address__content">
    <div class="section__title">
        <h2>住所の変更</h2>
    </div>
    <form action="/purchase/address/{{ $item->id }}" method="POST" class="address-form">
        @csrf
        <label for="zip_code" class="address-form__item-label">郵便番号</label>
        <input type="text" name="zip_code" id="zip_code" class="address-form__item-input"
            value="{{ old('zip_code', session("addressData_{$item->id}.zip_code")) }}" required>
        <div class="form__error">
            @error('zip_code')
            {{ $message }}
            @enderror
        </div>
        <label for="address" class="address-form__item-label">住所</label>
        <input type="text" name="address" id="address" class="address-form__item-input"
            value="{{ old('address', session("addressData_{$item->id}.address")) }}">
        <div class="form__error">
            @error('address')
            {{ $message }}
            @enderror
        </div>
        <label for="building" class="address-form__item-label">建物名</label>
        <input type="text" name="building" id="building" class="address-form__item-input" value="{{ old('building', session("addressData_{$item->id}.building")) }}">
        <div class="address-form__button">

            <button type="submit" class="address-form__button-submit">更新する</button>
        </div>
    </form>
</div>
@endsection