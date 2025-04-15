<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
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
Route::get('/sell', [ItemController::class, 'sell']);

//プロフィール編集画面の表示
Route::get('/mypage/profile', [UserController::class, 'profile']);

//プロフィール画面（マイページ）の表示
Route::get('/mypage', [UserController::class, 'showMypage']);

//商品詳細画面の表示
Route::get('/item', [ItemController::class, 'showItem']);
