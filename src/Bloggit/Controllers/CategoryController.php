<?php namespace Bloggit\Controllers;


/**
* Controller for category pages
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Repositories\PostRepository as PostRepo;


class CategoryController extends Controller
{

    public function getPage($request, $response, $args)
    {
        $category = $args['category'];
        $postRepo = new PostRepo($this->container->get('db'));
        $posts = $postRepo->getPostsByCategory($category);
        $args['posts'] = $posts->getCollection();
        return $this->view->render($response, 'category.twig', $args);
    }
}
