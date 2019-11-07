<?php namespace Bloggit\Repositories;

use PDO;


/**
* PostRepository model implements abstract methods in Repository abstract class
*
* used to read, update, delete posts
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Repositories\CommentRepository as CommentRepo;
use Bloggit\Repositories\UserRepository as UserRepo;
use Bloggit\Follow\Follow as Follow;


class PostRepository extends Repository
{

    /**
    * Get a single post for display in the view
    * @param itemId is required for correct behaviour in this implementation
    * populating @param table has no effect on behaviour in this implementation
    * @return Post object for the view
    */
    public function fetchSingle($title=NULL, $email=NULL, $username=NULL, $table=NULL, $userId=NULL, $itemId=NULL, String $condition=NULL)
    {
        try {
            $query = "SELECT * FROM posts";
            if (!is_null($itemId)) {
                $query .= " WHERE post_id = ?";
                $statement = $this->repo->prepare($query);
                $statement->bindParam(1, $itemId, PDO::PARAM_INT);
            } else if (!is_null($title)) {
                $query .= " WHERE title = ?";
                $statement = $this->repo->prepare($query);
                $statement->bindParam(1, $title, PDO::PARAM_STR);
            }
            $statement->execute();
            $data = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
        return new \Bloggit\Post(
            $data['title'],
            $data['access_level'],
            $data['author'],
            $data['published_on'],
            $data['body'],
            $data['post_id'],
            $data['category']
        );
    }


    /**
    * Get a collection of posts
    * @param table not required for correct DOMImplementation
    * @return PostCollection object
    */
    public function fetchAll($table=null, $col=NULL, $userId=NULL, $username=NULL, String $condition=NULL)
    {
        try {
            $query = "SELECT * FROM posts";
            if (!is_null($userId)) {
                $query .= " WHERE user_id = ? ORDER BY published_on DESC";
                $statement = $this->repo->prepare($query);
                $statement->bindParam(1, $userId, PDO::PARAM_STR);
                $statement->execute();
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            } else if (!is_null($username)) {
                $query .= " WHERE author = ? ORDER BY published_on DESC";
                $statement = $this->repo->prepare($query);
                $statement->bindParam(1, $username, PDO::PARAM_STR);
                $statement->execute();
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return false;
        }
        return new \Bloggit\Collections\PostCollection($data);
    }


    /**
    * Get a collection of posts by category
    * @param table not required for correct DOMImplementation
    * @return PostCollection object
    */
    public function getPostsByCategory($category)
    {
        try {
            $query = "SELECT * FROM posts WHERE category = ?
            AND access_level = 1 ORDER BY published_on DESC";
            $statement = $this->repo->prepare($query);
            $statement->bindParam(1, $category, PDO::PARAM_STR);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            return new \Bloggit\Collections\PostCollection($data);
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * Add a new post to the database
    * @param table null for correct implementation
    * @param values should contain associative array. Eg: ['user_id' => 1, 'title' => "Something", ... ]
    * keys for @param values should be the same as the column names for the posts table, but order is not important
    * @return true on successful insert
    */
    public function insert($table=NULL, Array $values, string $condition=NULL)
    {
        try {
            $query = "INSERT INTO posts (post_id, user_id, title, author, published_on, access_level, body, category)
                      VALUES (null, ?, ?, ?, ?, ?, ?, ?)";
            if (!is_null($condition)) {
                $query .= " WHERE {$condition}";
            }
            $statement = $this->repo->prepare($query);
            $statement->bindParam(1, $values['user_id'], PDO::PARAM_INT);
            $statement->bindParam(2, $values['title'], PDO::PARAM_STR);
            $statement->bindParam(3, $values['author'], PDO::PARAM_STR);
            $statement->bindParam(4, $values['published_on']);
            $statement->bindParam(5, $values['access_level'], PDO::PARAM_INT);
            $statement->bindParam(6, $values['body'], PDO::PARAM_STR);
            $statement->bindParam(7, $values['category'], PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * Delete a post and all associated comments
    * requires @param author username of post author
    * requires @param id post id number
    * @return bool true on successful execution
    */
    public function deletePost($author, $id)
    {
        $commentRepo = new CommentRepo($this->repo);
        try {
            $query = "DELETE FROM posts WHERE author = ? AND post_id = ?";
            $statement = $this->repo->prepare($query);
            $statement->bindParam(1, $author, PDO::PARAM_STR);
            $statement->bindParam(2, $id, PDO::PARAM_INT);
            if ($commentRepo->deleteAllComments($id) && $statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return true;
        }
    }


    public function updateAccessLevel($accessLevel, $author, $id)
    {
        try {
            $query = "UPDATE posts SET access_level = ? WHERE author = ? AND post_id = ?";
            $statement = $this->repo->prepare($query);
            $statement->bindParam(1, $accessLevel, PDO::PARAM_INT);
            $statement->bindParam(2, $author, PDO::PARAM_STR);
            $statement->bindParam(3, $id, PDO::PARAM_INT);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * Update a column in the posts table
    * @param table null for correct implementation
    * @param condition is recommended. Supply in string format without WHERE clause. Eg "user_id = 1";
    * @return true on successful update
    */
    public function update($table=NULL, $col, $value, String $condition=NULL)
    {
        try {
            $query = "UPDATE posts SET {$col} = {$value}";
            if (!is_null($condition)) {
                $query .= "WHERE {$condition}";
            }
            $statement = $this->repo->prepare($query);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function getNewsfeedPosts()
    {
        $follow = new Follow($this->repo);
        $following = $follow->getFollowing();
        $output = array();
        foreach ($following as $key => $value) {
            try {
                $query = "SELECT * FROM posts WHERE author = ? AND published_on >= CURDATE() + 30";
                $statement = $this->repo->prepare($query);
                $statement->bindParam(1, $value['username']);
                $statement->execute();
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $output[$value['username']] = $data;
                $output[$value['username']]['picture'] = UserRepo::getUserPic($this->repo, $value['username']);
            } catch (Exception $e) {
                return false;
            }
        }
        return $output;
    }
}
