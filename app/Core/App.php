<?php

class App
{
    public function __construct(private Router $router, private string $requestUri)
        {}
    public function run()
    {
        try {
            echo $this->router->resolve($this->requestUri);
        } catch (Exception $e) {
            var_dump($e);
        }
    }
}