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

Route::get('/','HomeController@index');

//Route::get('/home', 'HomeController@index');

Route::get('new-post', 'PostController@create');

Route::post('new-post','PostController@store');

Route::get('edit/{slug}','PostController@edit');

Route::post('update','PostController@update');

Route::get('delete/{id}','PostController@destroy');

Route::get('/{slug}',['as' => 'post', 'uses' => 'HomeController@show'])->where('slug', '[A-Za-z0-9-_]+');

Route::get('/user/{id}/posts','PostController@list');

Route::get('/user/{id}','UserController@profile')->where('id', '[0-9]+');

Route::post('/comment/add','CommentController@store');

Route::get('/auth/logout', 'Auth\LoginController@logout');