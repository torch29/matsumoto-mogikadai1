<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth')->group(function () {
    //商品出品画面の表示と出品
    Route::get('/sell', [ItemController::class, 'sell']);
    Route::post('/sell', [ItemController::class, 'store']);
});


//プロフィール編集画面の表示
Route::get('/mypage/profile', [UserController::class, 'profile']);

//プロフィール画面（マイページ）の表示
Route::get('/mypage', [UserController::class, 'mypage']);

//商品詳細画面の表示
Route::get('/item/{id}', [ItemController::class, 'detail']);

//商品購入画面の表示
Route::get('/purchase/{id}', [PurchaseController::class, 'index']);
