<?php namespace Bloggit\Controllers;


/**
* Controller for user profile view
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use DI\Container;
use Slim\Factory\AppFactory;
use Bloggit\Auth\Auth as Auth;
use Bloggit\Follow\Follow as Follow;
use Bloggit\Flash\FlashMessage as Flash;
use Bloggit\Pagination\Pagination as Pagination;
use Bloggit\Repositories\UserRepository as UserRepo;
use Bloggit\Repositories\PostRepository as PostRepo;


class ProfileController extends Controller
{

    public function getPage($request, $response, $args)
    {
        $userRepo = new UserRepo($this->container->get('db'));
        $user = $userRepo->fetchSingle(null, null, $args['username']);
        $postRepo = new PostRepo($this->container->get('db'));
        $posts = $postRepo->fetchAll(null, null, null, $args['username']);
        $posts = $posts->getCollection();

        // Pagination
        if (count($posts) > 4) {
            $pagination = new Pagination();
            $pagination->setTotalItems($posts);
            $pagination->setItemsPerPage(4);
            $pagination->setPages($posts);
            $pagination->setNumberOfPages();
            $posts = $pagination->pages;
            $params = $request->getQueryParams();
            $page = $params['page'];
            $args['paginated'] = true;
            $args['pages'] = $pagination->getNumberOfPages();
            $args['page'] = $page;
        }

        // Follow user being viewed
        if ($this->doFollow($request, $user->getUserName())) {
            return $response->withRedirect('/profile/' . $user->getUserName() . '?page=1');
        }

        // Check if active session user is following the user they are viewing
        if ($this->doCheckFollows($user->getUserName())) {
            $args['following'] = true;
        }

        // Unfollow the user being viewed
        if ($this->doUnfollow($request, $user->getUserName())) {
            return $response->withRedirect('/profile/' . $user->getUserName() . '?page=1');
        }

        // Add about blurb for user
        if ($request->isPost()) {
            $data = $request->getParsedBody();
            if (isset($data['about'])) {
                if ($userRepo->addAbout($user->getUserName(), $data['about'])) {
                    return $response->withRedirect('/profile/' . $user->getUserName() . '?page=1');
                }
            }
            // Profile picture upload
            if ($this->doPicUpload($user->getUserName())) {
                return $response->withRedirect('/profile/' . $user->getUserName() . '?page=1');
            }
        }

        $args['user'] = [
            'username' => $user->getUserName(),
            'about' => $user->getAbout(),
            'accountStatus' => $user->getAccountStatus(),
            'posts' => $posts,
        ];
        if (isset($args['paginated'])) {
            $args['user']['posts'] = $posts[$page - 1];
        }
        if (!is_null($user->getPicture())) {
            $args['user']['picture'] = '../../user_images/' . $user->getPicture();
        }
        if (Auth::isOwner($user->getUserName())) {
            $args['user']['owner'] = true;
        }
        return $this->view->render($response, 'profile.twig', $args);
    }
//-------------------------------------------------------------------------------------//


    private function doPicUpload($username)
    {
        if ($_FILES['profilePic']['size'] < 300000) {
            $extension = substr(basename($_FILES['profilePic']['name']), -4);
            if ($extension == '.jpg' || extension == '.png' || $extension == '.gif') {
                $userRepo = new UserRepo($this->container->get('db'));
                $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
                $directory = $root . '/user_images' . '/' . $username . $extension;
                move_uploaded_file($_FILES['profilePic']['tmp_name'], $directory);
                $userRepo->addPicture($username, $username . $extension);
                return true;
            }
        }
    }

    private function doFollow($request, $user)
    {
        if ($request->isGet()) {
            $params = $request->getQueryParams();
            if (isset($params['follow'])) {
                $follow = new Follow($this->container->get('db'));
                if ($follow->follow($user)) {
                    $flash = new Flash('success', 'Great! You\'re now following ' . $user . '!');
                    return true;
                }
            }
        }
        return false;
    }

    private function doCheckFollows($user)
    {
        $follow = new Follow($this->container->get('db'));
        if ($test = $follow->follows($user)) {
            return true;
        }
    }

    private function doUnfollow($request, $user)
    {
        if ($request->isGet()) {
            $params = $request->getQueryParams();
            if (isset($params['unfollow'])) {
                $follow = new Follow($this->container->get('db'));
                if ($follow->unfollow($user)) {
                    $flash = new Flash('warning', 'You\'re are no longer following ' . $user . '.');
                    return true;
                }
            }
        }
    }
}
