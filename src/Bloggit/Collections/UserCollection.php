<?php namespace Bloggit\Collections;


/**
* Collection of registered users
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\User;


class UserCollection extends Collection
{

    protected function setCollection($data)
    {
        foreach ($data as $key => $value) {
            $this->collection[] = new User(
                $value['username'],
                $value['email'],
                $value['password'],
                $value['user_id'],
                $value['full_name'];
            );
        }
    }

    public function getCollection()
    {
        return $this->collection;
    }
}
