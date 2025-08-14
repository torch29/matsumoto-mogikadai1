@component('mail::message')
# 取引完了のお知らせ

このたびはフリマアプリをご利用いただきありがとうございます。

{{ $purchase->purchasedUser->name }} さんが、「{{ $purchase->purchasedItem->name }}」の取引を完了しました。

アプリよりご確認のうえ、今回の取引の評価と完了をお願いいたします。

@component('mail::button', ['url' => route('mypage')])
マイページで確認する
@endcomponent

{{ config('app.name') }}
@endcomponent