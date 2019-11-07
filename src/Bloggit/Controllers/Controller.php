<?php namespace Bloggit\Controllers;

/**
* Base abstract class for all controller classes
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Psr\Container\ContainerInterface;


abstract class Controller
{

    protected $container;
    protected $view;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->setView();
    }

    protected function setView()
    {
        $this->view = $this->container->get('view');
    }

    abstract public function getPage($request, $response, $args);
}
