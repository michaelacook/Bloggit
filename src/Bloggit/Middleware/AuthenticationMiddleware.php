<?php namespace Bloggit\Middleware;


class AuthenticationMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['auth'])) {
            $this->container->view->getEnvironment()->addGlobal('auth', $_SESSION['auth']);
        }
        $response = $next($request, $response);
        return $response;
    }
}
