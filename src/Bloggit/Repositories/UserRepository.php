<?php namespace Bloggit\Repositories;


/**
* UserRepository model implements abstract methods in Repository abstract class
* for read, update, delete users
* an instance is required to create a new user in User::register()
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use PDO;
use Bloggit\User as User;


class UserRepository extends Repository
{


    /**
    * @param table not required for correct behaviour in this implementation
    * @return UserCollection instance
    */
    public function fetchAll($table = NULL, $col = NULL, $userId = NULL, $username = NULL, string $condition=NULL)
    {
        try {
            $query = "SELECT * FROM users";
            if (!is_null($id)) {
                $query .= " WHERE user_id = {$id}";
            }
            if (!is_null($condition)) {
                $query .= " AND {$condition}";
            }
            $statement = $this->repo->prepare($query);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
        return new UserCollection($data);
    }


    /**
    * @param table not required for correct behaviour in this implementation
    * @return User instance
    */
    public function fetchSingle($title=NULL, $email=NULL, $username=NULL, $table=NULL, $userId=NULL, $itemId=NULL, String $condition=NULL)
    {
        try {
            $query = "SELECT username, picture, email, password, about, user_id, full_name, account_status FROM users";
            if (!is_null($email)) {
                $query .= " WHERE email = ?";
                $statement = $this->repo->prepare($query);
                $statement->bindParam(1, $email, PDO::PARAM_STR);
            } else if (!is_null($itemId)) {
                $query .= " WHERE user_id = ?";
                $statement = $this->repo->prepare($query);
                $statement->bindParam(1, $userId, PDO::PARAM_STR);
            } else if (!is_null($username)) {
                $query .= " WHERE username = ?";
                $statement = $this->repo->prepare($query);
                $statement->bindParam(1, $username, PDO::PARAM_STR);
            }
            $statement->execute();
            $data = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
        return new User(
            $data['username'],
            $data['email'],
            $data['password'],
            $data['picture'],
            $data['about'],
            null,
            $data['user_id'],
            $data['full_name'],
            $data['account_status']
        );
    }


    /**
    * insert user data  into the database
    * @param table not needed for correct behaviour
    * @return bool true on successful insert
    */
    public function insert($table=NULL, Array $values, String $condition=NULL)
    {
        /* in the future this function should be refactored by use a foreach
        loop and dynamically add values to insert
        */
        try {
            $query = "INSERT INTO users (user_id, username, full_name,
                      email, password, account_status, activation_token)
                      VALUES (null, ?, ?, ?, ?, ?, ?)";
            $statement = $this->repo->prepare($query);
            $statement->bindParam(1, $values['username'], PDO::PARAM_STR);
            $statement->bindParam(2, $values['full_name'], PDO::PARAM_STR);
            $statement->bindParam(3, $values['email'], PDO::PARAM_STR);
            $statement->bindParam(4, $values['password'], PDO::PARAM_STR);
            $statement->bindParam(5, $values['account_status'], PDO::PARAM_STR);
            $statement->bindParam(6, $values['activation_token'], PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * for adding an about blurb to user profile
    */
    public function addAbout($username, $about)
    {
        try {
            $query = "UPDATE users SET about = ? WHERE username = ?";
            $statement = $this->repo->prepare($query);
            $statement->bindParam(1, $about, PDO::PARAM_STR);
            $statement->bindParam(2, $username, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function addPicture($username, $path)
    {
        try {
            $query = "UPDATE users SET picture = ? WHERE username = ?";
            $statement = $this->repo->prepare($query);
            $statement->bindParam(1, $path, PDO::PARAM_STR);
            $statement->bindParam(2, $username, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * change user data  in the database
    * @param table not needed for correct behaviour
    * @return bool true on successful update
    */
    public function update($table=null, $col, $value, String $condition=NULL)
    {
        try {
            $query = "UPDATE users SET {$col} = {$value}";
            if (!is_null($condition)) {
                $query .= " WHERE {$condition}";
            }
            $statement = $this->repo->prepare($query);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * Quick and simple static method for getting a user's picture file name
    */
    public static function getUserPic(PDO $db, $username)
    {
        try {
            $query = "SELECT picture FROM users WHERE username = ?";
            $statement = $db->prepare($query);
            $statement->bindParam(1, $username, PDO::PARAM_STR);
            $statement->execute();
            if ($output = $statement->fetch()) {
                return $output['picture'];
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
