<?php

use Services\Support\WebRoute as Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


$routes = Route::make();

Route::guard($request, function() {
    Route::get('home', '/', [PageController::class, 'renderHome']);
}, function() {
    Route::get('login', '/', [PageController::class, 'renderHome']);
});