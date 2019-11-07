<?php namespace Bloggit\Middleware;


/**
* Validation errors middleware for adding error messages to the global environment
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class ValidationErrorsMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['errors'])) {
            $this->container->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
            unset($_SESSION['errors']);
        }
        $response = $next($request, $response);
        return $response;
    }
}
