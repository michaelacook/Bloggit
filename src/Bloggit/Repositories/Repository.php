<?php namespace Bloggit\Repositories;


/**
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/

abstract class Repository
{

    protected $repo;

    public function __construct($dataSource)
    {
        $this->repo = $dataSource;
    }

    abstract public function fetchAll($table=null, $col=NULL, $userId=NULL, $username=NULL, String $condition=NULL);
    abstract public function fetchSingle($title=NULL, $email=NULL, $username=NULL, $table=NULL, $userId=NULL, $itemId=NULL, String $condition=NULL);
    abstract public function insert($table=NULL, Array $values, String $condition=NULL);
    abstract public function update($table=NULL, $col, $value, String $condition=NULL);
}
