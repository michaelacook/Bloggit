<?php namespace Bloggit\Pagination;


/**

* Custom pagination service class
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/



class Pagination
{

    private $totalItems;
    private $itemsPerPage;
    private $numberOfPages;
    private $currentPage;
    public $pages;

    public function setTotalItems($arr)
    {
        $this->totalItems = count($arr);
    }

    public function getTotalItems()
    {
        return $this->totalItems;
    }

    public function setItemsPerPage($numberOfItems)
    {
        $this->itemsPerPage = $numberOfItems;
    }

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function setPages($arr, $filterNullValues=false)
    {
        if (isset($this->itemsPerPage)) {
            $this->pages = array_chunk($arr, $this->itemsPerPage, true);
        }
    }

    public function setNumberOfPages()
    {
        if (isset($this->pages)) {
            $this->numberOfPages = count($this->pages);
        }
    }

    public function getNumberOfPages()
    {
        if (isset($this->numberOfPages)) {
            return $this->numberOfPages;
        }
    }

    public function setCurrentPage($page)
    {
        $this->currentPage = $page;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }
}
