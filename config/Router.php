<?php
require_once 'Exceptions/RouteNotFoundException.php';


use Exceptions\RouteNotFoundException;


class Router
{
    private string $basePath;
    private array $routes;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function register(string $path, callable|array $action): void
    {
        $this->routes[$path] = $action;
    }

    public function resolve(string $uri)
    {
        $parts = explode('?', $uri);
        $path = $parts[0];

        $params = $_GET;

        if ($this->basePath && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }
        //anti url sans / final
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        $action = $this->routes[$path] ?? null;

        if (is_callable($action)) {
            return $action($params);
        }
        if (is_array($action)) {
            [$className, $method] = $action;

            if (class_exists($className) && method_exists($className, $method)) {
                $class = new $className();

                // (pour get) dans [], on peut récupérer la suite du explode (exemple : explode('?', $uri)[1])
                // (pour un post) on met $_POST dans [], c'est tout.
                //exemple si c'est pour faire une inscription, la méthode handleSignup(array $post) {$post['username']}
                return call_user_func_array([$class, $method], [$params]);

            }
        }
        $errorController = new ErrorController();

        return $errorController->notFound();
    }
}