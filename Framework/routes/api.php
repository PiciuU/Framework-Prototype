<?php

use Services\Support\ApiRoute as Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

$routes = Route::make();

Route::post('user-register', '/register', [UserController::class, 'register']);
Route::post('user-login', '/login', [UserController::class, 'login']);

Route::guard($request, function() {
    Route::get('user-check', '/user', [UserController::class, 'user']);
    Route::get('user-logout', '/logout', [UserController::class, 'logout']);
});
