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

    Route::get('get-user-products', '/user/products', [UserProductController::class, 'index']);
    Route::get('get-user-product', '/user/products/{user_product_id}', [UserProductController::class, 'show'], array('user_product_id' => '[0-9]+'));
    Route::post('add-user-product', '/user/products', [ProductController::class, 'store']);
    Route::put('update-user-product', '/user/products', [UserProductController::class, 'update']);
    Route::delete('delete-user-product', '/user/products', [UserProductController::class, 'delete']);

    Route::post('add-subscription', '/subscription', [SubscriberController::class, 'create']);
    Route::put('update-subscription', '/subscription', [SubscriberController::class, 'update']);
    Route::delete('delete-subscription', '/subscription', [SubscriberController::class, 'delete']);
});

Route::get('bot-scrap-all', '/scrap', [ScrapController::class, 'scrapAll']);
Route::get('bot-scrap-one', '/scrap/{product_id}', [ScrapController::class, 'scrapOne'], array('product_id' => '[0-9]+'));
