<?php

class ErrorController extends BaseController
{
    public function notFound(): Renderer
    {
        http_response_code(404);
        return $this->render('errors/404');
    }

    public function forbidden(): Renderer
    {
        http_response_code(403);
        return $this->render('errors/403');
    }

    public function unauthorized(): Renderer
    {
        http_response_code(401);
        return $this->render('errors/401');
    }

    public function serverError(): Renderer
    {
        http_response_code(500);
        return $this->render('errors/500');
    }
}