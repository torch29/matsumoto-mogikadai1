@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection

@section('content')
<div class="profile__content">
    <div class="section__title">
        <h2>プロフィール設定</h2>
    </div>
    <form action="/mypage/profile" class="profile-form" method="post" enctype="multipart/form-data">
        @csrf
        <div class="profile-form__img-area">
            <div class="profile-form__img">
                @if (optional($user->profile)->profile_img)
                <img src="{{ asset( $user->profile->profile_img) }}" alt="">
                @else
                <div class="profile__icon-name">{{ mb_substr(Auth::user()->name, 0, 1 ) }}</div>
                @endif
            </div>
            <div class="profile-form__img-wrapper">
                <label for="img_path" class="profile-form__img-button--label">
                    画像を選択する
                    <input type="file" name="profile_img" id="img_path" class="profile-form__img-button">
                    <span id="selectedFileName" class="profile-form__filename"></span>
                </label>
            </div>
        </div>
        <div class="form__error">
            @error('profile_img')
            {{ $message }}
            @enderror
        </div>
        <label for="name" class="profile-form__item-label">
            ユーザー名
        </label>
        <input type="text" name="name" id="name" class="profile-form__item-input" value="{{ old('name', Auth::user()->name ?? '') }}">
        <label for="zip_code" class="profile-form__item-label">郵便番号</label>
        <input type="text" name="zip_code" id="zip_code" class="profile-form__item-input" value="{{ old('zip_code', $profile->zip_code ?? '') }}">
        <div class="form__error">
            @error('zip_code')
            {{ $message }}
            @enderror
        </div>
        <label for="address" class="profile-form__item-label">住所</label>
        <input type="text" name="address" id="address" class="profile-form__item-input" value="{{ old('address', $profile->address ?? '') }}">
        <div class="form__error">
            @error('address')
            {{ $message }}
            @enderror
        </div>
        <label for="building" class="profile-form__item-label">建物名</label>
        <input type="text" name="building" id="building" class="profile-form__item-input" value="{{ old('building', $profile->building ?? '') }}">
        <div class="profile-form__button">
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
            <button class="profile-form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
<script src="{{ asset('js/file_name_display.js') }}"></script>
@endsection