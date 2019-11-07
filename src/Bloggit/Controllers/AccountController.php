<?php namespace Bloggit\Controllers;


/**
* Controller for account settings page
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Auth\Auth as Auth;
use Bloggit\Flash\FlashMessage as Flash;
use Bloggit\Settings\Settings as Settings;
use Bloggit\Repositories\UserRepository as UserRepo;
use Bloggit\Repositories\PostRepository as PostRepo;
use Bloggit\Pagination\Pagination as Paginator;
use Respect\Validation\Validator as v;
use Bloggit\Follow\Follow as Follow;


class AccountController extends Controller
{

    public function getPage($request, $response, $args)
    {
        if (!Auth::requireAuth()) {
            return $response->withRedirect('/');
        }
        if ($request->isPost()) {
            $data = $request->getParsedBody();
            $args['data'] = $data;
            var_dump($args);
            $this->doUpdatePass($request);
            $this->doUpdateUsername($request);
            $this->doUpdateAccessLevel($request);
            if ($this->doUpdateEmail($request)) {
                return $response->withRedirect('/login');
            }
            if ($this->doDeleteAccount($request)) {
                return $response->withRedirect('/signup');
            }
            return $response->withRedirect('/account?page=1');
        }
        if ($this->doUnfollow($request)) {
            return $response->withRedirect('/account?page=1');
        }
        if ($this->doDeletePost($request)) {
            return $response->withRedirect('/account?page=1');
        }
        $follow = new Follow($this->container->get('db'));
        $postRepo = new PostRepo($this->container->get('db'));
        $posts = $postRepo->fetchAll(null, null, null, $_SESSION['auth']['username'])->getCollection();

        // Pagination
        if (count($posts) > 8) {
            $params = $request->getQueryParams();
            $page = $params['page'];
            $paginator = new Paginator();
            $paginator->setTotalItems($posts);
            $paginator->setItemsPerPage(8);
            $paginator->setPages($posts);
            $paginator->setNumberOfPages();
            $posts = $paginator->pages[$page - 1];
            $args['paginated'] = true;
            $args['pages'] = $paginator->getNumberOfPages();
            $args['page'] = $page;
        }
        $args['posts'] = $posts;
        $args['following'] = $follow->getFollowing();
        $args['followers'] = $follow->getFollowers();

        return $this->view->render($response, 'account.twig', $args);
    }

//-----------------------------------------------------------------------------//


    /**
    * Uses an instance of the Settings object and calls the updatePassword method
    */
    private function doUpdatePass($request)
    {
        $data = $request->getParsedBody();
        if (isset($data['newPassword']) && !is_null($data['newPassword'])) {
            if (!v::identical($data['newPassword'])->validate($data['confirmPassword'])) {
                $flash = new Flash('warning', 'The passwords you entered do not match. Please enter matching passwords.');
                return false;
            }
            $settings = new Settings($this->container->get('db'));
            if ($settings->updatePassword($_SESSION['auth']['id'], $data['newPassword'])) {
                $flash = new Flash('success', 'Success! Your password was updated.');
                return true;
            } else {
                $flash = new Flash('danger', 'Something went wrong. Try again later.');
                return false;
            }
        }
    }


    /**
    * Uses an instance of the Settings object and calls the UpdateUsername method
    */
    private function doUpdateUsername($request)
    {
        $data = $request->getParsedBody();
        $userRepo = new UserRepo($this->container->get('db'));
        if (isset($data['newUsername']) && !is_null($data['newUsername'])) {
            if (!v::identical($data['newUsername'])->validate($data['confirmUsername'])) {
                $flash = new Flash('warning', 'The usernames you entered do not match. Please enter matching usernames.');
                return false;
            }
            $settings = new Settings($this->container->get('db'));
            $user = $userRepo->fetchSingle(null, null, $data['newUsername']);
            if ($settings->updateUsername($data['newUsername'], $_SESSION['auth']['id'])) {
                $flash = new Flash('success', 'Success! Your username was updated.');
                return true;
            }
        }
        return false;
    }

    private function doUpdateEmail($request)
    {
        $data = $request->getParsedBody();
        if (isset($data['newEmail']) && !is_null($data['newEmail'])) {
            // validate email and return redirect and flash message on error
            if (!v::identical($data['newEmail'])->validate($data['confirmEmail'])) {
                $flash = new Flash('warning', 'The emails you entered do not match. Please enter matching emails.');
                return false;
            }
            $settings = new Settings($this->container->get('db'));
            if ($settings->updateEmail($_SESSION['auth']['id'], $data['newEmail'])) {
                //return redirect and flash message
                $flash = new Flash('success', 'Success! Your email was updated.
                You will receive an activation link in your email.
                Please click it to continue blogging!');
                return true;
            }
        }
        return false;
    }

    private function doDeleteAccount($request)
    {
        $data = $request->getParsedBody();
        if (isset($data['delete']) && !is_null($data['delete'])) {
            if (!v::identical($data['delete'])->validate($data['confirmDelete'])) {
                $flash = new Flash('warning', 'To delete your account please enter matching passwords.');
                return false;
            }
            $settings = new Settings($this->container->get('db'));
            if ($settings->deleteUserAccount($_SESSION['auth']['id'])) {
                return true;
            }
        }
        return false;
    }

    private function doUnfollow($request)
    {
        $data = $request->getQueryParams();
        if (isset($data['unfollow'])) {
            $follow = new Follow($this->container->get('db'));
            if ($follow->unfollow($data['unfollow'])) {
                $flash = new Flash('warning', 'You are no longer following ' . $data['unfollow'] . '.');
                return true;
            } else {
                return false;
            }
        }
    }

    private function doDeletePost($request)
    {
        if ($request->isGet()) {
            $params = $request->getQueryParams();
            if (isset($params['delete_post'])) {
                $postRepo = new PostRepo($this->container->get('db'));
                $author = $params['author'];
                $id = $params['id'];
                if ($postRepo->deletePost($author, $id)) {
                    $flash = new Flash('success', 'Your post was deleted.');
                    return true;
                }
                return false;
            }
        }
    }

    private function doUpdateAccessLevel($request)
    {
        $data = $request->getParsedBody();
        if (isset($data['accessLevel'])) {
            $postRepo = new PostRepo($this->container->get('db'));
            $author = $data['author'];
            $id = $data['postId'];
            $accessLevel = $data['accessLevel'];
            if ($postRepo->updateAccessLevel($accessLevel, $author, $id)) {
                return true;
            }
            return false;
        }
    }
}
