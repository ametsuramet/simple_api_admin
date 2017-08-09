<?php

Route::middleware(['web','guest'])->prefix('simple_admin')->group(function () {
    Route::get('/login', '\Amet\SimpleAdminAPI\Controllers\LoginController@getLogin');
    Route::get('/registration', '\Amet\SimpleAdminAPI\Controllers\LoginController@getRegistration');
    Route::get('/forgot', '\Amet\SimpleAdminAPI\Controllers\LoginController@getForgot');
    Route::post('/login', '\Amet\SimpleAdminAPI\Controllers\LoginController@postLogin');
    Route::post('/registration', '\Amet\SimpleAdminAPI\Controllers\LoginController@postRegistration');
});

Route::middleware(['web','auth'])->prefix('simple_admin')->group(function () {
    Route::get('/dashboard', '\Amet\SimpleAdminAPI\Controllers\DashboardController@index');
    Route::get('/logout', '\Amet\SimpleAdminAPI\Controllers\LoginController@getLogout');
});

Route::get('/login', ['as' => 'login' , function() {
	return redirect('simple_admin/login');
}]);


Route::get('/home', ['as' => 'home' , function() {
	return redirect('simple_admin/dashboard');
}]);