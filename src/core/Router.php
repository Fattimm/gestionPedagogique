<?php
namespace Src\Core;

class Router {
    private static $routes = [];

    public static function get($path, $handler) {
        self::$routes['GET'][$path] = $handler;
    }

    public static function post($path, $handler) {
        self::$routes['POST'][$path] = $handler;
    }

    public static function routePage($method, $uri, $database)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $callback = self::$routes[strtoupper($method)][$path] ?? null;
    
        if ($callback) {
            $controllerName = '\\Src\\App\\Controller\\' . $callback['Controller'];
            $actionName = $callback['action'];
    
            if (class_exists($controllerName)) {
                $controller = new $controllerName($database);
    
                if (method_exists($controller, $actionName)) {
                    $controller->$actionName();
                } else {
                    header("HTTP/1.0 404 Not Found");
                    echo "La méthode {$actionName} n'existe pas dans le contrôleur {$controllerName}.";
                }
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "Le contrôleur {$controllerName} n'existe pas.";
            }
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Page not found.";
        }
    }
    

    public static function getRoutes() {
        return self::$routes;
    }
}
