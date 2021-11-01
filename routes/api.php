<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Api')->prefix('v1')->group(function(){

    Route::namespace('Auth')->prefix('auth')->group(function(){
        Route::post('login', 'LoginJwtController@login');
        Route::get('logout', 'LoginJwtController@logout');
        Route::get('refresh', 'LoginJwtController@refresh');
    });

    Route::group(['middleware' => ['jwt.auth']], function () {

        Route::resource('real_states', 'RealStateController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters([
            'real_states.show' => 'slug'
        ]);

        Route::resource('real_states.photos', 'RealStatePhotoController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

        Route::resource('users', 'UserController', ['except' => ['create', 'edit']]);
        Route::resource('user/real_states', 'UserRealStateController', [
            'names' => [
                'index' => 'user.real_states.index',
                'store' => 'user.real_states.store',
                'show' => 'user.real_states.show',
                'update' => 'user.real_states.update',
                'destroy' => 'user.real_states.destroy',
            ],
            'except' => ['create', 'edit']
        ]);
        Route::resource('user/real_states.photos', 'RealStatePhotoController');

        /* Route::get('user/{id}/real_states', 'UserController@UserRealStates')->name('user.real_states'); */

        Route::get('categories/{id}/real_states', 'CategoryController@realStates')->name('categories.real_states');
        Route::resource('categories', 'CategoryController', ['except' => ['create', 'edit']]);

    });

});

