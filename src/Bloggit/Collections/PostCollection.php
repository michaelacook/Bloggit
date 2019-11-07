<?php namespace Bloggit\Collections;


/**
* Collection of Post Objects
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Post;


class PostCollection extends Collection
{

    protected function setCollection($data)
    {
        foreach ($data as $key => $value) {
            $this->collection[] = new Post(
                $value['title'],
                $value['access_level'],
                $value['author'],
                $value['published_on'],
                $value['body'],
                $value['post_id'],
                $value['category']
            );
        }
    }

    public function getCollection()
    {
        return $this->collection;
    }
}
