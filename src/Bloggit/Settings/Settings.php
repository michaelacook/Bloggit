<?php namespace Bloggit\Settings;


/**
* Model handling account settings
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use PDO;
use Bloggit\User;
use Bloggit\Auth\Auth as Auth;
use Bloggit\Email\Emailer as Emailer;
use Bloggit\Flash\FlashMessage as Flash;
use Bloggit\Activation\Activator as Activator;
use Bloggit\Repositories\UserRepository as UserRepo;


class Settings
{

    protected $db;

    /**
    * methods are for use inside the account view controller
    */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    /**
    * @param request should be an instance of the HTTP request
    */
    public function updatePassword($userid, $newpass)
    {
        $hashed = password_hash($newpass, PASSWORD_DEFAULT);
        try {
            $query = "UPDATE users SET password = ? WHERE user_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $hashed, PDO::PARAM_STR);
            $statement->bindParam(2, $userid, PDO::PARAM_INT);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function updateUsername($newUsername, $userid)
    {
        try {
            $query = "UPDATE users SET username = ? WHERE user_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $newUsername, PDO::PARAM_STR);
            $statement->bindParam(2, $userid, PDO::PARAM_INT);
            if (
                $this->updateFollowUsername($newUsername) &&
                $this->updateFollowFollows($newUsername) &&
                $statement->execute() &&
                $this->updatePostAuthor($newUsername, $userid) &&
                $this->updateCommentAuthor($newUsername, $userid)
            ) {
                $this->addUsernameToSession($newUsername);
                return true;
            }
        } catch (Exception $e) {
            return true;
        }
    }


    private function updateFollowUsername($newUserName)
    {
        $currentUser = $_SESSION['auth']['username'];
        try {
            $query = "UPDATE follows SET username = ? WHERE username = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $newUserName, PDO::PARAM_STR);
            $statement->bindParam(2, $currentUser, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    private function updateFollowFollows($newUserName)
    {
        $currentUser = $_SESSION['auth']['username'];
        try {
            $query = "UPDATE follows SET follows = ? WHERE follows = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $newUserName, PDO::PARAM_STR);
            $statement->bindParam(2, $currentUser, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    private function updateFollowing($newUserName)
    {
        $currentUser = $_SESSION['auth']['username'];
        try {
            $query = "UPDATE follows SET username = ? WHERE username = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $newUserName, PDO::PARAM_STR);
            $statement->bindParam(2, $currentUser, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    private function updatePostAuthor($newAuthorName, $userid)
    {
        try {
            $query = "UPDATE posts SET author = ? WHERE user_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $newAuthorName, PDO::PARAM_STR);
            $statement->bindParam(2, $userid, PDO::PARAM_INT);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    private function updateCommentAuthor($newAuthorName, $userid)
    {
        try {
            $query = "UPDATE comments SET username = ? WHERE user_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $newAuthorName, PDO::PARAM_STR);
            $statement->bindParam(2, $userid, PDO::PARAM_INT);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    private function addUsernameToSession($username)
    {
        $_SESSION['auth']['username'] = $username;
    }


    private function updateAccountStatus($userid)
    {
        try {
            $query = "UPDATE users SET account_status = 'inactive' WHERE user_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $userid, PDO::PARAM_INT);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    private function updateActivationToken($userid)
    {
        $token = Activator::generateToken();
        try {
            $query = "UPDATE users SET activation_token = ? WHERE user_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $token, PDO::PARAM_STR);
            $statement->bindParam(2, $userid, PDO::PARAM_INT);
            if ($statement->execute()) {
                return $token;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function updateEmail($userid, $newEmail)
    {
        $this->updateAccountStatus($userid);
        $username = $_SESSION['auth']['username'];
        try {
            $query = "UPDATE users SET email = ? WHERE user_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $newEmail, PDO::PARAM_STR);
            $statement->bindParam(2, $userid, PDO::PARAM_INT);
            if ($statement->execute()) {
                $token = $this->updateActivationToken($userid);
                $link = Activator::generateActivationLink('localhost', $token, $username);
                $emailer = new Emailer(new \PHPMailer\PHPMailer\PHPMailer(true));
                $emailer->addRecipient([
                    'address' => $newEmail,
                    'name' => $username
                ]);
                $emailer->setSubject('Confirm your new email address');
                $emailer->setBody(
                    "<h3>Activate Your New Email</h3><br />
                    <p>Hey there, " . $username . "! You recently updated your email address.
                    Click the link below to activate your new email!</p><br />"
                    . $link
                );
                $emailer->sendEmail();
                Auth::logout();
                $flash = new Flash('success', 'Success! Your username was updated.');
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    private function deletePrivatePosts($userid)
    {
        try {
            $query = "DELETE FROM posts WHERE user_id = ? AND access_level = 2";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $userid, PDO::PARAM_INT);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteUserProfilePic()
    {
        $img = UserRepo::getUserPic($this->db, $_SESSION['auth']['username']);
        $path = "../../user_images/";
        if (unlink($path . $img)) {
            return true;
        }
        return false;
    }

    private function deleteUser($userid)
    {
        try {
            $query = "DELETE FROM users WHERE user_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $userid, PDO::PARAM_INT);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteUserAccount($userid)
    {
        $count = 0;
        $userRepo = new UserRepo($this->db);
        $username = $_SESSION['auth']['username'];
        $user = $userRepo->fetchSingle(null, null, $username);
        $email = $user->getEmail();
        if ($this->deletePrivatePosts($userid)) {
            $count++;
        }
        $this->deleteUserProfilePic();
        $emailer = new Emailer(new \PHPMailer\PHPMailer\PHPMailer(true));
        $emailer->addRecipient([
            'address' => $email,
            'name' => $username
        ]);
        $emailer->setSubject('So long for now!');
        $emailer->setBody(
            "<h3>We'll Miss You!</h3><br />
            <p>Hey there, " . $username . "! All the best to you, and we
            hope to see you again someday!</p><br />"
        );
        $emailer->sendEmail();
        if ($this->deleteUser($userid)) {
            $count++;
            if ($count == 2) {
                Auth::logout();
                return true;
            }
        }
        return false;
    }
}
