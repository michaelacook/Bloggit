<?php namespace Bloggit\Collections;


/**
* Base abstract class for all collection objects
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


abstract class Collection
{

    protected $collection = array();

    public function __construct($data)
    {
        $this->setCollection($data);
    }

    abstract protected function setCollection($data);
    abstract public function getCollection();
}
