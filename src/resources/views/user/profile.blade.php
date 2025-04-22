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
                @if ($user->profile && $user->profile->profile_img)
                <img src="{{ asset( $user->profile->profile_img ) }}" alt="商品画像">
                {{--<p class="profile-form__img--circle"></p>--}}
                @else
                プロフィール画像はまだ登録されていません
                @endif
            </div>
            <label for="profile_img" class="profile-form__img-button--label">
                画像を選択する
                <input type="file" name="profile_img" id="profile_img" class="profile-form__img-button">
            </label>
        </div>
        <label for="name" class="profile-form__item-label">
            ユーザー名
        </label>
        <input type="text" name="name" id="name" class="profile-form__item-input" value="{{ Auth::user()->name }}">
        <label for="zip_code" class="profile-form__item-label">郵便番号</label>
        <input type="text" name="zip_code" id="zip_code" class="profile-form__item-input" value="{{ $profile->zip_code ?? '' }}">
        <label for="address" class="profile-form__item-label">住所</label>
        <input type="text" name="address" id="address" class="profile-form__item-input" value="{{ $profile->address ?? '' }}">
        <label for="building" class="profile-form__item-label">建物名</label>
        <input type="text" name="building" id="building" class="profile-form__item-input" value="{{ $profile->building ?? '' }}">
        <div class="profile-form__button">
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
            <button class="profile-form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection