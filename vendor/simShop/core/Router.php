<?php


namespace simFW;


use mysql_xdevapi\Exception;

class Router {

    protected static $routes = [];
    protected static $route = [];

    public static function add($regexp, $route = []) {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes() {
        return self::$routes;
    }

    public static function getRoute() {
        return self::$route;
    }

    public static function dispatch($url) {
        $url = self::removeQueryString($url);
        if (self::matchRoute($url)) {
          $controller = 'app\controllers\\' . self::$route['prefix'] . self::$route['controller'] . 'Controller';
          if (class_exists($controller)) {
                $controllerObj = new $controller(self::$route);
                $action = self::lowerCamelCase(self::$route['action']) . 'Action';
                if (method_exists($controllerObj, $action)) {
                    $controllerObj->$action();
                    $controllerObj->getView();
                } else {
                    throw  new \Exception("Метод $controller::$action не найден", 404);
                }
          } else {
              throw  new \Exception("Контролллер $controller не найден", 404);
          }
        } else {
            throw  new \Exception('Страница не найдена', 404);
        }
    }

    public static function matchRoute($url) {
        foreach (self::$routes as $pattern => $route) {
            if (preg_match("#$pattern#i", $url, $matches)) {
                foreach ($matches as $key => $val) {
                    if (is_string($key)) {
                        $route[$key] = $val;
                    }
                }
                if (empty($route['action'])) {
                    $route['action'] = 'index';
                }
                if (!isset($route['prefix'])) {
                    $route['prefix'] = '';
                } else {
                    $route['prefix'] .= '\\';
                }
                $route['controller'] = self::upperCamelCase($route['controller']);
                self::$route = $route;
                return true;
            }
        }
        return false;
    }

    protected static function upperCamelCase($name) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
        debug($name);
    }

    protected static function lowerCamelCase($name) {
        return lcfirst(self::upperCamelCase($name));
    }

    protected static function removeQueryString($url) {
        if ($url) {
            $params = explode('&', $url, 2);
            if (strpos($params[0], '=') === false) {
                return rtrim($params[0], '/');
            } else {
                return'';
            }

        }
    }
}