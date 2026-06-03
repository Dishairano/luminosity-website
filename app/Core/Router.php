<?php

namespace App\Core;

/**
 * Router met ondersteuning voor padparameters, bv. '/admin/klanten/{id}'.
 * Gevangen parameters worden als argumenten aan de controller-actie meegegeven,
 * ná het Request-object.
 */
final class Router
{
    /** @var array<int, array{method:string,regex:string,params:string[],action:array}> */
    private array $routes = [];

    public function get(string $path, array $action): void  { $this->add('GET', $path, $action); }
    public function post(string $path, array $action): void { $this->add('POST', $path, $action); }

    private function add(string $method, string $path, array $action): void
    {
        $params = [];
        $regex = preg_replace_callback('#\{([a-zA-Z_]+)\}#', function ($m) use (&$params) {
            $params[] = $m[1];
            return '([^/]+)';
        }, $path);
        $this->routes[] = [
            'method' => $method,
            'regex'  => '#^' . $regex . '$#',
            'params' => $params,
            'action' => $action,
        ];
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method) {
                continue;
            }
            if (preg_match($route['regex'], $request->path, $m)) {
                array_shift($m); // hele match weg
                [$class, $method] = $route['action'];
                $controller = new $class();
                $controller->$method($request, ...$m);
                return;
            }
        }
        http_response_code(404);
        (new View())->render('404', ['pad' => $request->path]);
    }
}
