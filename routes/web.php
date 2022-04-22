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

Route::get('/', [App\Http\Controllers\HomeController::class, 'homePage'])->name('homePage');

Auth::routes();

Route::group(['middleware' => 'auth'], function ()
{
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'chatTheme'])->name('chatTheme');
    Route::post('/user-chat', [App\Http\Controllers\HomeController::class, 'userChat'])->name('userChat');
    Route::post('/chat-message', [App\Http\Controllers\HomeController::class, 'chatMessage'])->name('chatMessage');

    Route::get('/post', [App\Http\Controllers\HomeController:: class, 'post'])->name('post');
    Route::get('/index', [App\Http\Controllers\HomeController:: class, 'index'])->name('post.index');
    Route::post('/post-upload', [App\Http\Controllers\HomeController:: class, 'postUpload'])->name('post.upload');
    Route::get('/post-edit/{id}', [App\Http\Controllers\HomeController:: class, 'postEdit'])->name('post.edit');
    Route::put('/post-update/{id}', [App\Http\Controllers\HomeController:: class, 'postUpdate'])->name('post.update');
    Route::post('/post-store', [App\Http\Controllers\HomeController:: class, 'postStore'])->name('post.store');

    // stripe routes 
    Route::get('/check', [App\Http\Controllers\HomeController:: class, 'check'])->name('check');
    Route::post('/check', [App\Http\Controllers\HomeController:: class, 'check'])->name('check.post');

    // paypal route 
    Route::get('success', [App\Http\Controllers\PaymentController:: class, 'success'])->name('success');
    Route::get('error', [App\Http\Controllers\PaymentController:: class, 'error'])->name('error');
    Route::post('payment-success', [App\Http\Controllers\PaymentController:: class, 'charge'])->name('paymany');
});

    