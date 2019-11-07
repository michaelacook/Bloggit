<?php namespace Bloggit\Controllers;


use Bloggit\Repositories\PostRepository as PostRepo;
use Bloggit\Repositories\UserRepository as UserRepo;
use Bloggit\Repositories\CommentRepository as CommentRepo;
use Respect\Validation\Validator as v;
use Bloggit\Follow\Follow as Follow;
use Bloggit\Flash\FlashMessage as Flash;
use Bloggit\Comment;


/**
* Controller for user profile view
*
* used to read, update, delete posts
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class PostViewController extends Controller
{


    public function getPage($request, $response, $args)
    {
        $postRepo = new PostRepo($this->container->get('db'));
        $commentRepo = new CommentRepo($this->container->get('db'));
        $post = $postRepo->fetchSingle(null, null, null, null, null, $args['id']);
        $post = $post->getPost();
        if ($commentCollection = $commentRepo->fetchAll($post['id'])) {
            $comments = $commentCollection->getCollection();
        }
        $userRepo = new UserRepo($this->container->get('db'));
        $author = $userRepo->fetchSingle(null, null, $post['author']);

        // Follow
        if ($this->doFollow($request, $author->getUserName())) {
            return $response->withRedirect('/' . $author->getUserName() . '/' . 'posts' . '/' . $post['id']);
        }

        // Unfollow
        if ($this->doUnfollow($request, $author->getUserName())) {
            return $response->withRedirect('/' . $author->getUserName() . '/' . 'posts' . '/' . $post['id']);
        }

        // Add comments to args
        if ($comments) {
            foreach ($comments as $key => $value) {
                $args['comments'][] = $value->getComment();
                // Add the comment author's profile image
                $args['comments'][$key]['picture'] = '../../user_images/' .
                    UserRepo::getUserPic($this->container->get('db'), $args['comments'][$key]['author']);
            }
        }
        $args['user']['username'] = $_SESSION['auth']['username'];


        // Add span with class for styling first letter of blog post
        $position = strpos($post['body'], $post['body'][3]);
        if ($position !== false) {
            $newBody = substr_replace(
                $post['body'],
                '<span class="first-letter">' . $post['body'][3] . '</span>',
                $position,
                strlen($post['body'][3])
            );
            $post['body'] = $newBody;
        }


        // check if active session user is following the post author
        if ($this->doCheckFollows($author->getUserName())) {
            $args['following'] = true;
        }


        //if user has a profile pic, add it to args
        if (!is_null($author->getPicture())) {
            $args['author']['picture'] = '../../user_images/' . $author->getPicture();
        }
        $args['post'] = $post;

        // Add comment to post
        if ($this->doAddComment($request, $args)) {
            return $response->withRedirect('/' . $author->getUserName() . '/' . 'posts' . '/' . $post['id'] . '#comments');
        }

        // Update comment
        if ($this->doUpdateComment($request)) {
            return $response->withRedirect('/' . $author->getUserName() . '/' . 'posts' . '/' . $post['id'] . '#comments');
        }

        // Delete a comment
        if ($this->doDeleteComment($request, $args)) {
            return $response->withRedirect('/' . $author->getUserName() . '/' . 'posts' . '/' . $post['id'] . '#comments');
        }

        return $this->view->render($response, 'post.twig', $args);
    }

    private function doFollow($request, $user) {
        if ($request->isGet()) {
            $params = $request->getQueryParams();
            if (isset($params['follow'])) {
                $follow = new Follow($this->container->get('db'));
                if ($follow->follow($user)) {
                    return true;
                }
            }
        }
    }

    private function doUnFollow($request, $user) {
        if ($request->isGet()) {
            $params = $request->getQueryParams();
            if (isset($params['unfollow'])) {
                $follow = new Follow($this->container->get('db'));
                if ($follow->unfollow($user)) {
                    return true;
                }
            }
        }
    }


    private function doCheckFollows($user)
    {
        $follow = new Follow($this->container->get('db'));
        if ($test = $follow->follows($user)) {
            return true;
        }
    }


    private function doUpdateComment($request)
    {
        if ($request->isPost()) {
            $params = $request->getQueryParams();
            if (isset($params['edit_comment'])) {
                $postdata = $request->getParsedBody();
                $author = $postdata['username'];
                $postid = $postdata['postid'];
                $commentid = $postdata['commentid'];
                $body = $postdata['body'];
                if (!v::stringType()->notEmpty()->validate($body)) {
                    $flash = new Flash('warning', 'You cannot submit a blank comment.');
                    return false;
                }
                $commentRepo = new CommentRepo($this->container->get('db'));
                if ($commentRepo->updateComment($author, $postid, $commentid, $body)) {
                    $flash = new Flash('success', 'Your comment was updated.');
                    return true;
                }
            }
        }
    }

    private function doAddComment($request, $args)
    {
        if ($request->isPost()) {
            $params = $request->getQueryParams();
            if (isset($params['add_comment'])) {
                $postdata = $request->getParsedBody();
                if (!v::stringType()->notEmpty()->validate($postdata['comment'])) {
                    $flash = new Flash('warning', 'You cannot submit a blank comment.');
                    return false;
                }
                $commentRepo = new CommentRepo($this->container->get('db'));
                if ($commentRepo->insert(
                    $args['id'],
                    $_SESSION['auth']['username'],
                    $postdata['comment'],
                    $_SESSION['auth']['id'],
                    date('Y-m-d')
                )) { return true; }
            }
        }
    }

    private function doDeleteComment($request, $args)
    {
        if ($request->isGet()) {
            $params = $request->getQueryParams();
            if (isset($params['delete_comment'])) {
                $postid = $args['id'];
                $author = $params['author'];
                $commentid = $params['comment_id'];
                $commentRepo = new CommentRepo($this->container->get('db'));
                if ($commentRepo->deleteComment($author, $postid, $commentid)) {
                    $flash = new Flash('success', 'Your comment was deleted.');
                    return true;
                }
            }
        }
    }
}
