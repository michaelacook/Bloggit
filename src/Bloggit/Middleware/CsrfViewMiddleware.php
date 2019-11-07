<?php namespace Bloggit\Middleware;


/**
* Add hidden form fields for cross-site request forgery protection to global envinronment
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class CsrfViewMiddleware extends Middleware
{

    /**
    * To use in a view, insert {{ csrf.field | raw }} inside the form tags 
    */
    public function __invoke($request, $response, $next)
    {
        $this->container->view->getEnvironment()->addGlobal('csrf', [
            'field' => '
            <input type="hidden" name="' . $this->container->csrf->getTokenNameKey() . '" value="' . $this->container->csrf->getTokenName() . '" />
            <input type="hidden" name="' . $this->container->csrf->getTokenValueKey() . '" value="' . $this->container->csrf->getTokenValue() . '" />',
        ]);
        $response = $next($request, $response);
        return $response;
    }
}
