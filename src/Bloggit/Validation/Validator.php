<?php namespace Bloggit\Validation;


/**
* Base class for all form input validators
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


class Validator
{

    protected $errors;


    /**
    * Add errors to $_SESSION so that middleware can add them to global environment
    */
    protected function addSessionErrors()
    {
        if (!empty($this->errors)) {
            $_SESSION['errors'] = $this->errors;
        }
    }


    /**
    * check for validation fail by checking if error messages exit
    * @return bool true on fail, else false
    */
    public function checkValidationFail()
    {
        if (!empty($this->errors)) {
            $this->addSessionErrors();
            return true;
        } else {
            return false;
        }
    }
}
