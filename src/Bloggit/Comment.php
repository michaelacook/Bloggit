<?php namespace Bloggit;


/**
* Repository for post comments
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class Comment
{

    protected $commentId;
    protected $postId;
    protected $userId;
    protected $author;
    protected $body;
    protected $date;

    public function __construct($commentid, $postid, $userid, $username, $body, $date)
    {
        $this->commentId = $commentid;
        $this->postId = $postid;
        $this->userId = $userid;
        $this->author = $username;
        $this->body = $body;
        $this->date = $date;
    }

    public function getComment()
    {
        $output = array();
        $output['author'] = $this->getAuthor();
        $output['body'] = $this->getBody();
        $output['date'] = $this->getDate();
        $output['commentId'] = $this->getCommentId();
        $output['userId'] = $this->getUserId();
        $output['postId'] = $this->getPostId();
        return $output;
    }

    protected function getDate()
    {
        return $this->date;
    }

    protected function getCommentId()
    {
        return $this->commentId;
    }

    protected function getPostId()
    {
        return $this->postId;
    }

    protected function getUserId()
    {
        return $this->commentId;
    }

    protected function getAuthor()
    {
        return $this->author;
    }

    protected function getBody()
    {
        return $this->body;
    }
}
