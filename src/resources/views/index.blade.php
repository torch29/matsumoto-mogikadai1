@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')


<ul class="main-tab__list">
    <button class="main-tab__button active" data-tab="all">おすすめ</button>
    <button class="main-tab__button" data-tab="myList">マイリスト</button>
</ul>
<div class="main-tab__content">
    user_id: {{ Auth::id() }}　（そのidのものは表示しない状態、id=1なら 1は表示されない） {{-- 確認用あとで消す --}}
    {{-- ここから"おすすめ"のタブ --}}
    <div class="tab-panel" id="all">
        おすすめを表示したい
        <ul class="item-card__content">
            @foreach($items as $item)
            @continue($item->user_id == Auth::id() )
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    @if($item->status == 'available')
                    <a href="/item/{{ $item->id }}">
                        <img src="{{ $item->img_path }}" class="item-card__content--img" alt="商品画像">
                        <p>{{ $item->name }}</p>
                    </a>
                    @else
                    <a href="/item/{{ $item->id }}">
                        <img src="{{ $item->img_path }}" class="item-card__content--sold-img" alt="商品画像">
                        <div class="item-sold">sold</div>
                        <p>{{ $item->name }}</p>
                    </a>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    {{-- ここからマイリストのタブ --}}
    <div class="tab-panel" id="myList" style="display: none;">
        マイリストを表示したい
        <ul class="item-card__content">
            @foreach($myLists as $myList)
            <li class="item-card__content--list">
                <div class="item-card__content-inner">
                    @if($myList->status == 'available')
                    <a href="/item/{{ $myList->id }}">
                        <img src="{{ $myList->img_path }}" class="item-card__content--img" alt="商品画像">
                        <p>{{ $myList->name }}</p>
                    </a>
                    @else
                    <a href="/item/{{ $myList->id }}">
                        <img src="{{ $myList->img_path }}" class="item-card__content--sold-img" alt="商品画像">
                        <div class="item-sold">sold</div>
                        <p>{{ $myList->name }}</p>
                    </a>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
<script>
    const buttons = document.querySelectorAll('.main-tab__button');
    const contents = document.querySelectorAll('.tab-panel');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.getAttribute('data-tab');

            //すべてのボタンからactiveを外す
            buttons.forEach(btn => btn.classList.remove('active'));
            //クリックされたボタンにactiveをつける
            button.classList.add('active');

            contents.forEach(content => {
                if (content.id === tab) {
                    content.style.display = 'block';
                } else {
                    content.style.display = 'none';
                }
            });
        });
    });

    /*
        tabItems.forEach((tabItem) => {
            tabItem.addEventListener("click", () => {
                //すべてのタブを非アクティブにする
                tabItems.forEach((t) => {
                    t.classList.remove("active");
                });

                //すべてのコンテンツを非表示にする
                const tabPanels = document.querySelectorAll(".tab-panel");
                tabPanels.forEach((tabPanel) => {
                    tabPanel.classList.remove("active");
                });

                //クリックされたタブをアクティブにする
                tabItem.classList.add("active");

                //対応するコンテンツを表示
                const tabIndex = Array.from(tabItems).indexOf(tabItem);
                tabPanels[tabIndex].classList.add("active");
            });
        });
        */
</script>
@endsection