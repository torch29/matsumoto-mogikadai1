@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection

@section('content')
<div class="verify__content">
    <div class="verify__content-text">
        <div class="">
            <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p>メール認証を完了してください。</p>
        </div>
        <form class="" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">認証メールを再送する</button>
        </form>
        @if (session('message'))
        <div class="verify__alert-success">
            {{ session('message') }}
        </div>
        @endif
    </div>
</div>
@endsection