<?php namespace Bloggit\Search;

/**
* Search for posts, users, etc
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use PDO;


class Search
{

    protected $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
    * Search for matching posts
    * @return bool false on fail or associative array of results
    */
    public function searchPosts($searchTerms)
    {
        if ($searchTerms == "" || is_null($searchTerms)) {
            return false;
        }
        try {
            $query = "SELECT * FROM posts WHERE title LIKE '%{$searchTerms}%'";
            $statement = $this->db->prepare($query);
            // $statement->bindParam(1, $searchTerms, PDO::PARAM_STR);
            $statement->execute();
        } catch (Exception $e) {
            return false;
        }
        if ($result = $statement->fetchAll(PDO::FETCH_ASSOC)) {
            return $result;
        }
    }
}
