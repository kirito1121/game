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

Route::get('/test', function () {
    return view('test');
});
Route::get('/saga', function () {
    return view('saga');
});
Route::get('/sagaByVersion', function () {
    return view('sagaVersion');
});
Route::get('/rush', function () {
    return view('rush');
});
Route::get('/level', function () {
    return view('level');
});
Route::get('/conversion', function () {
    return view('conversion');
});

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
