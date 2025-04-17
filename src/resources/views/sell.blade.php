@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-content">
    <div class="sell-content__title">
        <h2>商品の出品</h2>
    </div>
    <form action="/sell?user_id={{ Auth::id() }}" class="sell-form" method="post" enctype="multipart/form-data">
        @csrf
        <div class="sell-form__item">
            <label class="sell-form__item-label">商品画像</label>
            <div class="sell-form__img-area">
                <label for="img_path" class="sell-form__img-button--label">
                    画像を選択する
                    <input type="file" name="img_path" id="img_path" class="sell-form__img-button">
                </label>
            </div>
        </div>
        <div class="sell-form__item">
            <h3 class="sell-form__title">商品の詳細</h3>
            <label class="sell-form__item-label">カテゴリー</label>
            <div class="sell-form__category-area">
                @foreach ($categories as $category)
                <input type="checkbox" name="category_ids[]" class="sell-form__category-checkbox" value="{{ $category->id }}" id="category_{{ $category->id }}">
                <label for="category_{{ $category->id }}">{{ $category['content'] }}</label>
                @endforeach
            </div>
        </div>
        <div class="sell-form__item">
            <label class="sell-form__item-label">商品の状態</label>
            <select name="condition" id="" class="sell-form__item-select">
                <option value="" selected>選択してください</option>
                @foreach ($conditions as $key => $condition)
                <option value="{{ $key }}">{{ $condition }}</option>
                @endforeach
            </select>
        </div>
        <div class="sell-form__item">
            <h3>商品名と説明</h3>
            <label for="name" class="sell-form__item-label">商品名</label>
            <input type="text" name="name" id="name" class="sell-form__item-input" value="{{ old('name') }}">

            <label for="brand_name" class="sell-form__item-label">ブランド名</label>
            <input type="text" name="brand_name" id="brand_name" class="sell-form__item-input" value="{{ old('brand_name') }}">

            <label for="explain" class="sell-form__item-label">商品の説明</label>
            <textarea name="explain" id="explain" class="sell-form__item-textarea">
            {{ old('explain') }}</textarea>
            <label for="price" class="sell-form__item-label">販売価格</label>
            <input type="number" name="price" id="price" class="sell-form__item-input" placeholder="￥" value="{{ old('price') }}">
        </div>
        <div class="sell-form__button">
            <button type="submit" class="sell-form__button-submit">出品する</button>
        </div>
    </form>
</div>
@endsection