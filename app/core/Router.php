<?php

namespace App\Core;

use App\Modules\Usuarios\Helpers\AccessControl;

class Router
{
    protected $routes = [];

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    protected function addRoute($method, $uri, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    public function dispatch($uri)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $auth = AccessControl::authorize($uri, $method);

        if (empty($auth['allowed'])) {
            if (!empty($auth['redirect'])) {
                header('Location: ' . $auth['redirect']);
                return;
            }

            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->match($route['uri'], $uri, $params)) {
                $this->executeAction($route['action'], $params);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 Not Found";
    }

    protected function match($routeUri, $requestUri, &$params)
    {
        $routePattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routeUri);
        $routePattern = "#^" . $routePattern . "$#";

        if (preg_match($routePattern, $requestUri, $matches)) {
            array_shift($matches); // Remove full match
            $params = $matches;
            return true;
        }

        return false;
    }

    protected function executeAction($action, $params)
    {
        if (is_callable($action)) {
            call_user_func_array($action, $params);
        } elseif (is_array($action)) {
             $controller = $action[0];
             $method = $action[1];
             
             if (!class_exists($controller)) {
                 // echo "CRITICAL Router: Controller class $controller not found<br>";
                 return;
             }
             
             $instance = new $controller();
             if (!method_exists($instance, $method)) {
                 // echo "CRITICAL Router: Method $method not found in $controller<br>";
                 return;
             }
             
             call_user_func_array([$instance, $method], $params);
        } elseif (is_string($action)) {
            list($controllerName, $method) = explode('@', $action);
            
            if (!class_exists($controllerName)) {
                 // echo "CRITICAL Router: Controller class '$controllerName' not found<br>";
                 return;
            }

            $controller = new $controllerName();
            call_user_func_array([$controller, $method], $params);
        }
    }
}
