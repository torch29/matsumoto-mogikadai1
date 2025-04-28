<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\StripeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index']);
//商品詳細画面の表示
Route::get('/item/{id}', [ItemController::class, 'detail']);

//認証を要するルート
Route::middleware('auth')->group(function () {
    //商品詳細画面からコメントをする
    Route::post('comment', [ItemController::class, 'postComment']);
    //商品詳細画面からいいね機能の利用
    Route::post('/favorite/{item_id}', [FavoriteCOntroller::class, 'favorite']);
    Route::delete('/favorite/{item_id}', [FavoriteController::class, 'removeFavorite']);
    //商品出品画面の表示と出品
    Route::get('/sell', [ItemController::class, 'sell']);
    Route::post('/sell', [ItemController::class, 'store']);
    //商品購入画面の表示
    Route::get('/purchase/{id}', [PurchaseController::class, 'purchase']);
    Route::post('/purchase/{id}', [PurchaseController::class, 'decidePurchase']);
    //配送先変更画面
    Route::get('/purchase/address/{id}', [PurchaseController::class, 'changeAddress']);
    Route::post('/purchase/address/{id}', [PurchaseController::class, 'saveShippingAddress']);
    //stripe checkout関連
    //Route::get('payment', function () {
    //    return view('payment');});
    //Route::post('payment', [StripeController::class, 'payment']);
    Route::post('/purchase/checkout/{itemId}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/success', function () {
        return view('purchase.success'); // 成功画面を後で作る
    })->name('purchase.success');
    Route::get('/purchase/cancel', function () {
        return view('purchase.cancel'); // キャンセル画面を後で作る
    })->name('purchase.cancel');

    //マイページとプロフィールページのグループ
    Route::prefix('mypage')->group(function () {
        //プロフィール編集画面の表示と更新
        Route::get('/profile', [UserController::class, 'profile']);
        Route::post('/profile', [UserController::class, 'updateProfile']);
        //マイページの表示
        Route::get('', [UserController::class, 'showMypage']);
    });
});

Route::middleware('guest')->group(
    function () {
        Route::get('/register', [RegisteredUserController::class, 'create']);
        Route::post('/register', [RegisteredUserController::class, 'store']);
    }
);
