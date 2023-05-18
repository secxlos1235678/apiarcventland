<?php

use Illuminate\Http\Request;

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
Route::apiResource('v1/int/comments', 'API\v1\int\CommentController')->middleware('auth:api');


Route::group( [ 'prefix' => 'v1/int', 'namespace' => 'API\v1\int' ], function () {
	Route::group(['middleware' => 'auth:api'], function(){
		Route::get('/', 'PostsController@index');
		Route::post('p', 'PostsController@store');
		Route::delete('p/{post}', 'PostsController@destroy');
		Route::get('p/{post}', 'PostsController@show');
		Route::post('p/{post}', 'PostsController@update');
		Route::get('like/explore', 'PostsController@explore');		
		Route::get('posts', 'PostsController@vue_index');
		
		Route::post('like/{like}', 'LikeController@update2');
		
		Route::get('profile/{user}/edit', 'ProfilesController@edit');
		Route::get('profile/{user}', 'ProfilesController@index');
		Route::patch('profile/{user}', 'ProfilesController@update');
		Route::any('search', 'ProfilesController@search');
		
		Route::post('follow/{user}', 'FollowsController@store');
	
		//Route::get('stories/create', 'StoryController@create');
		Route::get('stories/{user}', 'StoryController@show');
		Route::post('stories', 'StoryController@store');
	});
});

