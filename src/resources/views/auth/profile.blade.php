@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection

@section('content')
<div class="profile__content">
    <div class="section__title">
        <h2>プロフィール設定</h2>
    </div>
    <form action="/updateProfile" class="profile-form" method="post" enctype="multipart/form-data">
        @csrf
        <label class="profile-form__item-label">商品画像</label>
        <div class="profile-form__img-area">
            <label for="profile_image" class="profile-form__img-button--label">
                画像を選択する
                <input type="file" name="profile_image" id="profile_image" class="profile-form__img-button">
            </label>
            <label for="name" class="profile-form__item-label">
                ユーザー名
            </label>
            <input type="text" name="name" id="name" class="profile-form__item-input" value="{{ Auth::user()->name }}">
            {{-- 登録されている名前が表示されるようにする --}}
            <label for="zip_code" class="profile-form__item-label">郵便番号</label>
            <input type="text" name="zip_code" id="zip_code" class="profile-form__item-input" value="">
            <label for="address" class="profile-form__item-label">住所</label>
            <input type="text" name="address" id="address" class="profile-form__item-input" value="{{ old('address') }}">
            <label for="building" class="profile-form__item-label">建物名</label>
            <input type="text" name="building" id="building" class="profile-form__item-input" value="{{ old('building') }}">
            <div class="profile-form__button">
                <button class="profile-form__button-submit" type="submit">更新する</button>
            </div>
    </form>
</div>
@endsection