<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('pushLevel', 'LevelController@pushLevel');
Route::post('pushLevels', 'LevelController@pushLevels');
Route::get('getLevel', 'LevelController@getLevel');
Route::get('getLevels', 'LevelController@getLevels');
Route::get('readDataMapApi', 'LevelController@readDataMapApi');
Route::get('playDataLevel', 'LevelController@playDataLevel');
