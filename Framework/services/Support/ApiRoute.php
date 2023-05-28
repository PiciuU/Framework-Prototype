<?php

namespace Services\Support;

use Services\Authenticable\HasAuthToken;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

class ApiRoute {
    private static $routes;

    public static function make() {
        self::$routes = new RouteCollection();
        return self::$routes;
    }

    public static function get($name, $path, $controller, $params = []) {
        self::$routes->add($name, new SymfonyRoute(
            config('APP_URL_SUBFOLDER').'/api'.$path,
            array('controller' => $controller[0], 'method' => $controller[1]),
            $params
        ));
        self::$routes->get($name)->SetMethods('GET');
    }

    public static function post($name, $path, $controller) {
        self::$routes->add($name, new SymfonyRoute(
            config('APP_URL_SUBFOLDER').'/api'.$path,
            array('controller' => $controller[0], 'method' => $controller[1])
        ));
        self::$routes->get($name)->setMethods('POST');
    }

    public static function put($name, $path, $controller) {
        self::$routes->add($name, new SymfonyRoute(
            config('APP_URL_SUBFOLDER').'/api'.$path,
            array('controller' => $controller[0], 'method' => $controller[1])
        ));
        self::$routes->get($name)->setMethods('PUT');
    }

    public static function delete($name, $path, $controller) {
        self::$routes->add($name, new SymfonyRoute(
            config('APP_URL_SUBFOLDER').'/api'.$path,
            array('controller' => $controller[0], 'method' => $controller[1])
        ));
        self::$routes->get($name)->setMethods('DELETE');
    }

    public static function guard(Request $request, \Closure $next) {
        $middleware = new HasAuthToken($request);
        if (!$middleware->isAuthorized()) return false;

        $next();
    }
}