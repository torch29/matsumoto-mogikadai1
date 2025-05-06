@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item__content-wrapper">
    <div class="item__img-area">
        @if($item->status == 'available')
        <img src=" {{ asset($item -> img_path) }}" class="item__img" alt="商品画像">
        @elseif( in_array($item->id, $purchasedItemIds) )
        <img src="{{ asset($item -> img_path) }}" class="item__img" alt="商品画像">
        <div class="item-purchasedItem"><span>購入しました</span></div>
        @else
        <img src="{{ asset($item->img_path) }}" class="item__img--sold" alt="商品画像">
        <div class="item-sold">sold</div>
        @endif
    </div>
    <div class="item__content">
        <div class="item__content-heading">
            @if(session('error'))
            <div class="item__alert">
                {{ session('error') }}
            </div>
            @endif
            <h2>{{ $item -> name }}</h2>
            <p class="heading--brand-name">{{ $item -> brand_name }}</P>
            <p class="heading--price">￥<span>{{ number_format($item->price) }}</span>（税込）</p>
            <div class="heading__icon-wrapper">
                <div class="heading__icons">
                    @if ( Auth::check() && Auth::user()->favoriteItems->contains($item->id) )
                    <form action="/favorite/{{ $item->id }}" class="item__favorite-form" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="item__favorite-form--button"><i class="fa-solid fa-star"></i>
                        </button>
                    </form>
                    @else
                    <form action="/favorite/{{ $item->id }}" method="post">
                        @csrf
                        <button type="submit" class="item__favorite-form--button"><i class="fa-regular fa-star"></i>
                        </button>
                    </form>
                    @endif
                    <p class="header--count">{{ $item->favoriteUsers()->count() }}</p>
                </div>
                <div class="heading__icons">
                    <i class="fa-regular fa-comment"></i>
                    <p class="header--count">
                        {{ count($item->comments) }}
                    </p>
                </div>
            </div>
            @if( $item->user_id == Auth::id() )
            @elseif( $item->status == "available" )
            <div class="item__content-button">
                <a href="/purchase/{{ $item->id }}" class="item__content-button--submit">購入手続きへ</a>
            </div>
            @else
            <div class="item__content-error--sold">
                この商品は売り切れです
            </div>
            @endif
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
                        <span class="status__category-content">{{ $category->content }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="item__content-status--condition">
                    <div class="status__condition-label">商品の状態</div>
                    <div class="status__condition-content">{{ $item -> getSelectedCondition() }}</div>
                </div>
            </div>
            <div class="item__content-comment">
                <div class="item__content-label">商品へのコメント
                </div>
                <form action="/comment" class="item__comment-form" method="post">
                    @csrf
                    <div class="item__content-item">
                        <textarea name="comment" class="item__content-textarea">{{ old('comment') }}</textarea>
                    </div>
                    <div class="item__content-button">
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <button class="item__content-button--submit">コメントを送信する</button>
                        <div class="form__error">
                            @error('comment')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>
                </form>
                <h3 class="item__content-label">
                    コメント ( {{ count($item->comments) }} )
                </h3>
                <div class="view__comment">
                    @foreach ($item->comments as $comment)
                    <div class="view__comment-inner">
                        <div class="view__comment--icon">
                            @if($comment->user->profile)
                            <img src="{{ asset( $comment->user->profile->profile_img) }}" alt="">
                            @else
                            <img src="" alt="No profile">
                            @endif
                        </div>
                        <div class="view__comment--name">
                            {{ $comment->user->name }}
                        </div>
                    </div>
                    <div class="view__comment--text">
                        <p>{!! nl2br($comment->comment) !!}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endsection