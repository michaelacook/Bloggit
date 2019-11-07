<?php namespace Bloggit\Collections;


/**
* Collection of comment objects
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Comment;


class CommentCollection extends Collection
{

    protected function setCollection($data)
    {
        foreach ($data as $key => $value) {
            $this->collection[] = new Comment(
                $value['comment_id'],
                $value['post_id'],
                $value['user_id'],
                $value['username'],
                $value['body'],
                $value['published_on']
            );
        }
    }

    public function getCollection()
    {
        return $this->collection;
    }
}
