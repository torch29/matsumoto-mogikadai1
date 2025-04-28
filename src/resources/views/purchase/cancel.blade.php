@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection

@section('content')
<div class="success__content">
    <h3>購入がキャンセルされました</h3>
    <div class="success__content--link">
        <a href="/">
            トップページへ
        </a>
    </div>
</div>
@endsection