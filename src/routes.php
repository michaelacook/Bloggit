<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Bloggit\Post;
use Bloggit\Controllers\SignupController;
use Bloggit\Controllers\HomeController;
use Bloggit\Controllers\LoginController;
use Bloggit\Controllers\NewPostController;
use Bloggit\Controllers\SearchController;
use Bloggit\Controllers\ProfileController;
use Bloggit\Controllers\PostViewController;
use Bloggit\Controllers\AccountController;
use Bloggit\Controllers\CategoryController;


return function (App $app) {
    $container = $app->getContainer();
    $app->get('/', HomeController::class . ':getPage');
    $app->map(['GET', 'POST'], '/signup', SignupController::class . ':getPage')->add(
        // Add middleware for form persistence
        new Bloggit\Middleware\SignupFormPersistenceMiddleware($container)
    );
    $app->map(['GET', 'POST'], '/login', LoginController::class . ':getPage');
    $app->map(['GET', 'POST'], '/new', NewPostController::class . ':getPage');
    $app->get('/search', SearchController::class . ':getPage');
    $app->map(['GET', 'POST'], '/{username}/posts/{id}', PostViewController::class . ':getPage');
    $app->map(['GET', 'POST'], '/profile/{username}', ProfileController::class . ':getPage');
    $app->map(['GET', 'POST'], '/account', AccountController::class . ':getPage');
    $app->map(['GET', 'POST'], '/categories/{category}', CategoryController::class . ':getPage');
};
