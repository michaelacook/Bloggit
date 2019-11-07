<?php namespace Bloggit\Controllers;


/**
* Controller for home page
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Flash\FlashMessage as Flash;
use Bloggit\Repositories\PostRepository as PostRepo;


class HomeController extends Controller
{

    public function getPage($request, $response, $args)
    {
        $args['pageStatus'] = 'active';
        $this->container->get('logger')->info("Home page");
        $postRepo = new PostRepo($this->container->get('db'));
        $newsFeed = $postRepo->getNewsfeedPosts();
        $args['newsfeed'] = $newsFeed;
        return $this->view->render($response, 'home.twig', $args);
    }
}
