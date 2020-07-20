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

Route::get('versions', 'VersionController@index');
Route::get('viewLevel', 'LevelController@viewLevel');
Route::post('pushLevel', 'LevelController@pushLevel');
Route::post('pushLevels', 'LevelController@pushLevels');
Route::get('getLevel', 'LevelController@getLevel');
Route::get('getLevels', 'LevelController@getLevels');
Route::get('readDataMapApi', 'LevelController@readDataMapApi');
Route::get('readDataMapABC', 'LevelController@readDataMapABC');
Route::get('playDataLevel', 'LevelController@playDataLevel');
Route::get('trackDataLevelByVersion', 'LevelController@trackDataLevelByVersion');

Route::get('versions', 'VersionController@index');
