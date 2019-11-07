<?php namespace Bloggit\Flash;


/**
* Flash messages

* PHP version > 7.1.27
* @category PHP
* @author Mark Railton <https://github.com/railto> <https://www.markrailton.com/>
* Modified by Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class FlashMessage
{

    public function __construct($type, $message)
    {
        $this->markup = $this->add($type, $message);
    }


    /**
    * save flash message to session
    * @param $type
    * @param $message
    */
    private function add($type, $message)
    {
        $markup = $this->createMarkup($type, $message);
        $_SESSION['flashMessage'] = $markup;
    }


    /**
    * Generate markup for flash message
    * @return string
    */
    private function createMarkup($type, $message)
    {
        return '<div class="container alert alert-'. $type . ' border border-' . $type . '" role="alert" id="alert">
                <span class="sr-only">Close</span></button>' . $message . '</div>';
    }
}
