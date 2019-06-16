<?php
//use Illuminate\Http\Request;

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');


// Route::middleware('auth:api')->get('/user', function(Request $request){
//    return $request->user();
// });
Route::group(['middleware' => ['jwt.auth']], function() {
   Route::get('profile', 'AuthController@getAuthenticatedUser');
   
});