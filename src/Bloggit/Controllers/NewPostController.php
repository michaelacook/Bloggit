<?php namespace Bloggit\Controllers;


/**
* Controller for new post page
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Auth\Auth as Auth;
use Bloggit\Repositories\PostRepository as PostRepo;


class NewPostController extends Controller
{

    public function getPage($request, $response, $args)
    {
        Auth::requireAuth($response, '/signup');
        if ($request->isGet()) {
            $args['pageStatus'] = 'active';
            $this->container->get('logger')->info("New post");
            return $this->view->render($response, 'new.twig', $args);
        }
        if ($request->isPost()) {
            $params = $request->getParsedBody();
            $values = array();
            $values['published_on'] = date('Y-m-d');
            $values['user_id'] = $_SESSION['auth']['id'];
            $values['author'] = $_SESSION['auth']['username'];
            $values['category'] = $params['category'];
            $values['title'] = $params['title'];
            $values['body'] = $params['body'];
            $values['access_level'] = $params['accessLevel'];
            $postRepo = new PostRepo($this->container->get('db'));
            if ($postRepo->insert(null, $values)) {
                $post = $postRepo->fetchSingle($values['title']);
                $post = $post->getPost();
                return $response->withRedirect($_SESSION['auth']['username'] . '/posts/' . $post['id']);
            }
        }
    }
}
