@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<nav class="main-nav">
    <ul class="main-nav__list">
        <li class="main-nav__list-item">おすすめ</li>
        <li class="main-nav__list-item"><a href="">マイリスト</a></li>
    </ul>
</nav>
<div class="main-content">
    user_id: {{ Auth::id() }}　（そのidのものは表示しない状態、id=1なら 1は表示されない） {{-- 確認用あとで消す --}}
    <div class="item-card__container">
        <ul class="item-card__content">
            @foreach($items as $item)
            @continue($item->user_id == Auth::id() )
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    @if($item->status == 'available')
                    <a href="/item/{{ $item->id }}">
                        <img src="{{ $item->img_path }}" class="item-card__content--img" alt="商品画像">
                    </a>
                    @else
                    <img src="{{ $item->img_path }}" class="item-card__content--sold-img" alt="商品画像">
                    <div class="item-sold">sold</div>
                    @endif
                </div>
                <p>{{ $item->name }}</p>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection