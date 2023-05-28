<?php

namespace App\Controllers;

use App\Controllers\UserProductController;

use App\Models\UserProduct;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Services\Support\Response;
use Services\Support\Validator;

class PageController {

	public function renderHome(Request $request, RouteCollection $routes) {
		$controller = new UserProductController();
		$products = $controller->index($request, false);

		require_once config('APP_ROOT').'/resources/views/home.php';
	}

	public function renderLogin(Request $request, RouteCollection $routes) {
		require_once config('APP_ROOT').'/resources/views/home.php';
	}
}