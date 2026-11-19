<?php

namespace Core;

use Core\Middleware\Authenticated;
use Core\Middleware\Guest;
use Core\Middleware\Middleware;
use Http\controllers\notes\NotesController;

class Router
{
    protected $routes = [];

    public function add($method, $uri, $controller)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
            'middleware' => null
        ];

        return $this;
    }

    public function get($uri, $controller)
    {
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        return $this->add('POST', $uri, $controller);
    }

    public function delete($uri, $controller)
    {
        return $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri, $controller)
    {
        return $this->add('PATCH', $uri, $controller);
    }

    public function put($uri, $controller)
    {
        return $this->add('PUT', $uri, $controller);
    }

    public function only($key)
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;

        return $this;
    }

    public function route($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                Middleware::resolve($route['middleware']);

                if($route['controller'] === 'NotesController.php') {
                    $this->handleNotes($uri, $method);
                    return;
                }

                return require base_path('Http/controllers/' . $route['controller']);
            }
        }

        $this->abort();
    }

    public function previousUrl()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function abort($code = 404)
    {
        http_response_code($code);

        require base_path("views/{$code}.php");

        die();
    }

    public function handleNotes($uri, $method){
        $instance = new NotesController();

        if($method === 'GET'){
            if ($uri == "/notes"){
                $instance->index();
            } elseif ($uri == "/note"){
                $instance->show();
            } elseif ($uri == "/notes/create"){
                $instance->create();
            }

            $instance->edit();
        }

        if($method === 'POST'){
            $instance->store();
        }

        if($method === 'PATCH'){
            $instance->update();
        }

        if($method === 'DELETE'){
            $instance->destroy();
        }
    }
}
