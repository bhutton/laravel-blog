<?php

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

Auth::routes();

Route::get('/','PostController@index');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('new-post', 'PostController@create');

Route::post('new-post','PostController@store');

Route::get('edit/{slug}','PostController@edit');

Route::post('update','PostController@update');

Route::get('delete/{id}','PostController@destroy');

Route::get('/{slug}',['as' => 'post', 'uses' => 'PostController@show'])->where('slug', '[A-Za-z0-9-_]+');

Route::get('/auth/logout', 'Auth\LoginController@logout');