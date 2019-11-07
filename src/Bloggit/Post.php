<?php namespace Bloggit;


/**

* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class Post
{
    public $title;
    public $accessLevel;
    public $author;
    public $category;
    public $date;
    public $body;
    public $id;

    /**
    * @param accessLevel 1 for public, 2 for private
    */
    public function __construct($title, $accessLevel, $author, $date, $body, $id, $category)
    {
        $this->setTitle($title);
        $this->setAccessLevel($accessLevel);
        $this->setAuthor($author);
        $this->setDate($date);
        $this->setBody($body);
        $this->setId($id);
        $this->setCategory($category);
    }

    public function getPost()
    {
        $output = array();
        $output['title'] = $this->title;
        $output['accessLevel'] = $this->accessLevel;
        $output['author'] = $this->author;
        $output['date'] = $this->date;
        $output['body'] = $this->body;
        $output['id'] = $this->id;
        $output['category'] = $this->category;
        return $output;
    }

    /**
     * getters and setters below
    */
    protected function setTitle($title)
    {
        $this->title = filter_var($title, FILTER_SANITIZE_STRING);
    }

    protected function setAccessLevel($accessLevel)
    {
        $this->accessLevel = filter_var($accessLevel, FILTER_SANITIZE_NUMBER_INT);
    }

    protected function setAuthor($author)
    {
        $this->author = filter_var($author, FILTER_SANITIZE_STRING);
    }

    protected function setDate($date)
    {
        $this->date = $date;
    }

    protected function setBody($body)
    {
        $this->body = filter_var($body);
    }

    protected function setId($id)
    {
        $this->id = $id;
    }

    protected function setCategory($category)
    {
        $this->category = $category;
    }
}
