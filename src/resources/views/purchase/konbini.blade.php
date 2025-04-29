@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection

@section('content')
<div class="stripe-konbini__content">
    <h3>コンビニ払いのご案内</h3>
    <p>別ウィンドウにて、コンビニ払い決済の画面が開きます。</p>
    <p>手順に従って、お支払いの手続きを進めてください。</p>
</div>

<script>
    window.open("{{ $checkoutUrl }}", "_blank");
</script>
@endsection