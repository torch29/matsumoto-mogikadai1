<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ChatController;

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

Route::middleware('guest')->group(
    function () {
        Route::get('/register', [RegisteredUserController::class, 'create']);
        Route::post('/register', [RegisteredUserController::class, 'store']);
    }
);

//トップページ
Route::get('/', [ItemController::class, 'index']);
//商品詳細画面の表示
Route::get('/item/{id}', [ItemController::class, 'detail']);

// メール認証
Route::get('/email/verify', function () {
    return view('user.verify');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//認証を要するルート
Route::middleware(['auth', 'verified'])->group(function () {
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
    //配送先変更画面
    Route::get('/purchase/address/{id}', [PurchaseController::class, 'changeAddress']);
    Route::post('/purchase/address/{id}', [PurchaseController::class, 'saveShippingAddress']);
    // stripe checkoutへ遷移して決済する
    Route::post('/purchase/checkout/{itemId}', [PurchaseController::class, 'decidePurchase'])->name('purchase.checkout');

    //マイページとプロフィールページのグループ
    Route::prefix('mypage')->group(function () {
        //プロフィール編集画面の表示と更新
        Route::get('/profile', [UserController::class, 'profile']);
        Route::post('/profile', [UserController::class, 'updateProfile']);
        //マイページの表示
        Route::get('', [UserController::class, 'showMypage']);
        Route::get('/chat/{id}', [ChatController::class, 'index']);
        Route::post('/chat', [ChatController::class, 'send']);
    });
});
