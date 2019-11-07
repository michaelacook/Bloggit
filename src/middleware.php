<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();
    $app->add(new \Bloggit\Middleware\FlashMessageMiddleware($container));
    $app->add(new \Bloggit\Middleware\ValidationErrorsMiddleware($container));
    $app->add(new \Bloggit\Middleware\AuthenticationMiddleware($container));
    $app->add(new \Bloggit\Middleware\CsrfViewMiddleware($container));
    $app->add($container->csrf);
};
