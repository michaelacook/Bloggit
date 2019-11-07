<?php namespace Bloggit\Middleware;


/**
* Middleware for persisting siugnup form data after validation error redirect
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class SignupFormPersistenceMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['old'])) {
            $this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
        }
        $_SESSION['old'] = $request->getParsedBody();
        $response = $next($request, $response);
        return $response;
    }
}
