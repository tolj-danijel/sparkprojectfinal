<?php

namespace App\Middleware;


class AdminMiddleware extends Middleware
{
    public function __invoke($request,$response,$next)
    {
        
        if(!$this->container->auth->checkIfAdmin())
        {
            $this->container->flash->addMessage('danger','You do not have the privileges to access this route!');
            return $response->withRedirect($this->container->router->pathFor('home'));
        }
     
    $response=$next($request,$response);

    return $response;

    }
}