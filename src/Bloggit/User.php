<?php namespace Bloggit;


/**
* base model for user objects
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class User
{

    protected $fullName;
    protected $userName;
    protected $about;
    protected $password;
    protected $email;
    protected $userId;
    protected $accountStatus = 'inactive';
    protected $activationToken;
    protected $picture;

    public function __construct($username, $email, $pass, $picture=NULL, $about=NULL, $token=NULL, $id=NULL, $fullname=NULL, $accountStatus=NULL)
    {
        if (!is_null($fullname)) {
            $this->setFullName($fullname);
        }
        if (!is_null($id)) {
            $this->setUserId($id);
        }
        if (!is_null($token)) {
            $this->setToken($token);
        }
        if (!is_null($about)) {
            $this->about = $about;
        }
        if (!is_null($accountStatus)) {
            $this->accountStatus = $accountStatus;
        }
        if (!is_null($picture)) {
            $this->picture = $picture;
        }
        $this->userName = $username;
        $this->setEmail($email);
        $this->setPassword($pass);
    }


    /**
    * accepts an instance of UserRepository to call its insert() method
    * @return bool true on successful insert
    */
    public function register(\Bloggit\Repositories\UserRepository $userRepository)
    {
        $values = array();
        if (isset($this->fullName)) {
            $values['full_name'] = $this->fullName;
        }
        $values['username'] = $this->userName;
        $values['email'] = $this->email;
        $values['password'] = password_hash($this->password, PASSWORD_DEFAULT);
        $values['account_status'] = $this->accountStatus;
        $values['activation_token'] = $this->activationToken;
        if ($userRepository->insert('users', $values)) {
            return true;
        }
        return false;
    }

    protected function setToken($token)
    {
        $this->activationToken = $token;
    }

    protected function setFullName($fullname)
    {
        $this->fullName = filter_var($fullname, FILTER_SANITIZE_STRING);
    }

    protected function setUserName($username)
    {
        $this->userName = filter_var($username, FILTER_SANITIZE_STRING);
    }

    public function getUserName()
    {
        return $this->userName;
    }

    protected function setPassword($pass)
    {
        $this->password = filter_var($pass, FILTER_SANITIZE_STRING);
    }

    public function getPassword()
    {
        return $this->password;
    }

    protected function setEmail($email)
    {
        $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public function getEmail()
    {
        return $this->email;
    }

    protected function setUserId($id)
    {
        $this->userId = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
    }

    public function getUserId()
    {
        return $this->userId;
    }

    protected function setAccountStatus($accountStatus)
    {
        $this->accountStatus = $accountStatus;
    }

    public function getAccountStatus()
    {
        return $this->accountStatus;
    }

    public function getAbout()
    {
        if (isset($this->about)) {
            return $this->about;
        }
    }

    public function getPicture()
    {
        return $this->picture;
    }
}
