@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection

@section('content')
<div class="success__content">
    <h3>購入が完了しました</h3>
    <p>ご利用ありがとうございます</p>
    <div class="success__content--link">
        <a href="/">
            トップページへ
        </a>
    </div>
</div>
@endsection