<?php namespace Bloggit\Repositories;


/**
* Repository for post comments
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use PDO;
use Bloggit\Comment;
use Bloggit\Collections\CommentCollection;


class CommentRepository
{

    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    /**
    * Insert new comment into database
    * @return bool true on successful insert
    */
    public function insert($postid, $username, $body, $userid, $publishedOn)
    {
        try {
            $query = "INSERT INTO comments (comment_id, post_id, username, body, user_id, published_on)
                      VALUES (null, ?, ?, ?, ?, ?)";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $postid, PDO::PARAM_INT);
            $statement->bindParam(2, $username);
            $statement->bindParam(3, $body, PDO::PARAM_STR);
            $statement->bindParam(4, $userid, PDO::PARAM_INT);
            $statement->bindParam(5, $publishedOn);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * Get a single comment
    * @return Comment object
    */
    public function fetchSingle($username)
    {
        try {
            $query = "SELECT * FROM comments WHERE username = ?";
            $statement = $this->db->prepare($query);
            $statement->prepare($query);
            $statement->bindParam(1, $username, PDO::PARAM_STR);
            $statement->execute();
            if ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
                return new Comment(
                    $data['comment_id'],
                    $data['post_id'],
                    $data['user_id'],
                    $data['username'],
                    $data['body'],
                    $data['published_on']
                );
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
    * Get all comments for a post
    * @return CommentCollection object
    */
    public function fetchAll($postid)
    {
        try {
            $query = "SELECT * FROM comments WHERE post_id = ? ORDER BY published_on DESC";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $postid, PDO::PARAM_INT);
            $statement->execute();
            if ($data = $statement->fetchAll(PDO::FETCH_ASSOC)) {
                return new CommentCollection($data);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateComment($author, $postid, $commentid, $body)
    {
        try {
            $query = "UPDATE comments SET body = ? WHERE post_id = ? AND comment_id = ? AND username = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $body, PDO::PARAM_STR);
            $statement->bindParam(2, $postid, PDO::PARAM_INT);
            $statement->bindParam(3, $commentid, PDO::PARAM_INT);
            $statement->bindParam(4, $author, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteAllComments($postid)
    {
        try {
            $query = "DELETE FROM comments WHERE post_id = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $postid, PDO::PARAM_INT);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteComment($author, $postid, $commentid)
    {
        try {
            $query = "DELETE FROM comments WHERE post_id = ? AND comment_id = ? AND username = ?";
            $statement = $this->db->prepare($query);
            $statement->bindParam(1, $postid, PDO::PARAM_INT);
            $statement->bindParam(2, $commentid, PDO::PARAM_INT);
            $statement->bindParam(3, $author, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function addLike($author, $postid, $commentid)
    {
        // increment likes for a comment
    }
}
