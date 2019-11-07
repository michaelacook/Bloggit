<?php namespace Bloggit\Auth;


use PDO;


class Auth
{

    protected $userRepo;

    public function __construct(\Bloggit\Repositories\UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function login($email, $password)
    {
        $user = $this->userRepo->fetchSingle(null, $email);
        if (password_verify($password, $user->getPassword()) &&
            $user->getAccountStatus() == 'active') {
            $_SESSION['auth'] = [
                'id' => $user->getUserId(),
                'username' => $user->getUserName(),
                'accountStatus' => $user->getAccountStatus()
            ];
            return true;
        } else {
            return false;
        }
    }


    /**
    * Static method for determining ownership of an entity
    * accepts @param username and checks it against the authenticated username
    * if username is same as authenticated username in session then @return bool true
    * else @return bool false
    * @return bool false if no user is authenticated
    */
    public static function isOwner($username=null, $userid=null)
    {
        if (!isset($_SESSION['auth'])) {
            return false;
        }
        if (!is_null($username)) {
            if ($username == $_SESSION['auth']['username']) {
                return true;
            } else {
                return false;
            }
        } else if (!is_null($userid)) {
            if ($userid == $_SESSION['auth']['id']) {
                return true;
            } else {
                return false;
            }
        }
    }


    /**
    * @param response must be the HTTP response object
    * @param uri must be the resource the unathenticated user is redirected to
    * i.e, $uri = '/signup';
    */
    public static function requireAuth()
    {
        if (!isset($_SESSION['auth'])) {
            return false;
        } else {
            return true;
        }
    }

    public static function logout()
    {
        if (isset($_SESSION['auth'])) {
            unset($_SESSION['auth']);
            session_destroy();
        }
    }
}
