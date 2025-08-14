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
                    <span id="selectedFileName" class="sell-form__filename"></span>
                </label>
            </div>
        </div>
        <div class="form__error">
            @error('img_path')
            {{ $message }}
            @enderror
        </div>
        <div class="sell-form__item">
            <h3 class="sell-form__title">商品の詳細</h3>
            <label class="sell-form__item-label">カテゴリー</label>
            <div class="sell-form__category-area">
                @foreach ($categories as $category)
                <div class="sell-form__label-wrapper">
                    <label for="category_{{ $category->id }}" class="sell-form__category-label">
                        <input type="checkbox" name="category_ids[]" class="sell-form__category-checkbox" value="{{ $category->id }}" id="category_{{ $category->id }}" {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                        <span>
                            {{ $category['content'] }}
                        </span>
                    </label>
                </div>
                @endforeach
            </div>
            <div class="form__error">
                @error('category_ids')
                {{ $message }}
                @enderror
            </div>
        </div>
        <div class="sell-form__item">
            <label class="sell-form__item-label">商品の状態</label>
            <div class="sell-form__select-wrapper">
                <select name="condition" id="" class="sell-form__item-select">
                    <option value="" {{ old('condition')=='' ? 'selected' : '' }}>選択してください</option>
                    @foreach ($conditions as $key => $condition)
                    <option value="{{ $key }}" {{ old('condition') == $key ? 'selected' : '' }}>{{ $condition }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form__error">
                @error('condition')
                {{ $message }}
                @enderror
            </div>
        </div>
        <div class="sell-form__item">
            <h3>商品名と説明</h3>
            <label for="name" class="sell-form__item-label">商品名</label>
            <input type="text" name="name" id="name" class="sell-form__item-input" value="{{ old('name') }}">
            <div class="form__error">
                @error('name')
                {{ $message }}
                @enderror
            </div>
            <label for="brand_name" class="sell-form__item-label">ブランド名</label>
            <input type="text" name="brand_name" id="brand_name" class="sell-form__item-input" value="{{ old('brand_name') }}">
            <div class="form__error">
                @error('brand_name')
                {{ $message }}
                @enderror
            </div>
            <label for="explain" class="sell-form__item-label">商品の説明</label>
            <textarea name="explain" id="explain" class="sell-form__item-textarea">{{ old('explain') }}</textarea>
            <div class="form__error">
                @error('explain')
                {{ $message }}
                @enderror
            </div>
            <label for="price" class="sell-form__item-label">販売価格</label>
            <input type="number" name="price" id="price" class="sell-form__item-input" placeholder="￥" value="{{ old('price') }}">
            <div class="form__error">
                @error('price')
                {{ $message }}
                @enderror
            </div>
        </div>
        <div class="sell-form__button">
            <button type="submit" class="sell-form__button-submit">出品する</button>
        </div>
    </form>
</div>
<script src="{{ asset('js/file_name_display.js') }}"></script>
@endsection