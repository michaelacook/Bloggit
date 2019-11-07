<?php namespace Bloggit\Follow;


/**
* Model for following and unfollowing users
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use PDO;
use Bloggit\Repositories\UserRepository as UserRepo;


class Follow
{

    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    /**
    * Follow a user
    * @param follows is the user being followed
    * @return bool true on successful follow
    */
    public function follow($follows)
    {
        $username = $_SESSION['auth']['username'];
        try {
            $query = "INSERT INTO follows (username, follows) VALUES (?, ?)";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $username, PDO::PARAM_STR);
            $statement->bindParam(2, $follows, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * Unfollow a user
    * @param unfollows is the user being unfollowed
    * @return bool true on successful unfollow
    */
    public function unfollow($unfollows)
    {
        $username = $_SESSION['auth']['username'];
        try {
            $query = "DELETE FROM follows WHERE username = ? AND follows = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $username, PDO::PARAM_STR);
            $statement->bindParam(2, $unfollows, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * Check if the active session user follows another user
    * @param user is username
    * @return bool true if following, false if not
    */
    public function follows($user)
    {
        $activeuser = $_SESSION['auth']['username'];
        try {
            $query = "SELECT follows FROM follows WHERE username = ? AND follows = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $activeuser, PDO::PARAM_STR);
            $statement->bindParam(2, $user, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result['follows'] == $user) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function getFollowers()
    {
        $user = $_SESSION['auth']['username'];
        try {
            $query = "SELECT username FROM follows WHERE follows = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $user, PDO::PARAM_STR);
            $statement->execute();
            if ($result = $statement->fetchAll(PDO::FETCH_ASSOC)) {
                $output = array();
                foreach ($result as $key => $value) {
                    $output[] = [
                        'username' => $value['username'],
                        'picture' => UserRepo::getUserPic($this->db, $value['username'])
                    ];
                }
                return $output;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function getFollowing()
    {
        $user = $_SESSION['auth']['username'];
        try {
            $query = "SELECT follows FROM follows WHERE username = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $user, PDO::PARAM_STR);
            $statement->execute();
            if ($result = $statement->fetchAll(PDO::FETCH_ASSOC)) {
                $output = array();
                foreach ($result as $key => $value) {
                    $output[] = [
                        'username' => $value['follows'],
                        'picture' => UserRepo::getUserPic($this->db, $value['follows'])
                    ];
                }
                return $output;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
