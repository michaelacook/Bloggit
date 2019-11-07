<?php namespace Bloggit\Controllers;


/**
* Controller for login page
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Repositories\UserRepository as UserRepo;
use Bloggit\Activation\Activator;
use Bloggit\Auth\Auth as Auth;
use PDO;


class LoginController extends Controller
{

    public function getPage($request, $response, $args)
    {
        $args['pageStatus'] = 'active';
        if ($request->isGet()) {
            $params = $request->getQueryParams();
            if (isset($params['user']) && isset($params['t'])) {
                $user = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_STRING);
                $token = filter_input(INPUT_GET, 't', FILTER_SANITIZE_STRING);
                if (!Activator::activate($token, $user, $this->container->get('db'))) {
                    return $response->withRedirect('/login?activation=fail');
                }
                /* maybe send a welcome email telling the user their account is now active
                   also log them in automatically on activation
                */
                return $response->withRedirect('/login');
            }
            if (isset($params['logout'])) {
                Auth::logout();
                return $response->withRedirect('/login');
            }
            return $this->view->render($response, 'login.twig', $args);
        } else if ($request->isPost()) {
            $data = $request->getParsedBody();
            $auth = new Auth(new UserRepo($this->container->get('db')));
            if ($auth->login($data['email'], $data['password'])) {
                $username = $_SESSION['auth']['username'];
                return $response->withRedirect('/profile/' . $username . '?page=1');
            }
        }
    }
}
