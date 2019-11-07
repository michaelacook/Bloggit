<?php namespace Bloggit\Middleware;


/**
* Base class for all custom middleware
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/



class Middleware
{

    // must be the DiC
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
}
