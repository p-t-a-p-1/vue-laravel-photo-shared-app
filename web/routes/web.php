<?php

use Illuminate\Support\Facades\Route;

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

// homeページ
Route::get('/home', 'HomeController@index')->name('home');

// 写真ダウンロード
Route::get('/photos/{photo}/download', 'PhotoController@download');

// Laravelのルート定義は上から順番にマッチしたルートに制御が渡されるため、
// 追加する場合はこれより上に書く
// API以外は全てindexテンプレを参照
Route::get('/{any?}', function () {
    return view('index');
})->where('any', '.+');
Auth::routes();