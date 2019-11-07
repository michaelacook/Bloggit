<?php namespace Bloggit\Controllers;


/**
* Controller for search page
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\Search\Search as Search;


class SearchController extends Controller
{

    public function getPage($request, $response, $args)
    {
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['search_terms'])) {
            $args['search_terms'] = $queryParams['search_terms'];
        }
        $search = new Search($this->container->get('db'));
        var_dump($search);
        $results = $search->searchPosts($queryParams['search_terms']);
        $args['results'] = $results;
        var_dump($args);
        return $this->view->render($response, 'search.twig', $args);
    }
}
