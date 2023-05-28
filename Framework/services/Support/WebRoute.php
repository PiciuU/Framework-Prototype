<?php

namespace Services\Support;

use Services\Authenticable\HasAuthToken;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

class WebRoute {
    private static $routes;

    public static function make() {
        self::$routes = new RouteCollection();
        return self::$routes;
    }

    public static function get($name, $path, $controller, $params = []) {
        self::$routes->add($name, new SymfonyRoute(
            config('APP_URL_SUBFOLDER').$path,
            array('controller' => $controller[0], 'method' => $controller[1]),
            $params
        ));
    }

    public static function guard(Request $request, \Closure $next, \Closure $callback) {
        $middleware = new HasAuthToken($request);
        if (!$middleware->isAuthorized()) $callback();
        else $next();
    }
}