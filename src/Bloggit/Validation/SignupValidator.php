<?php namespace Bloggit\Validation;


/**
* Validation for signup form input
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use PDO;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;


class SignupValidator extends Validator
{


    /**
    * Checks if a username already exists in the database, @return bool true if passes
    * @return bool false on fail, adds errors to error property
    * Receives @param PDO connection
    */
    public function usernameExists($username, PDO $db)
    {
        try {
            $query = "SELECT * FROM users WHERE username = ?";
            $statement = $db->prepare($query);
            $statement->bindParam(1, $username, PDO::PARAM_STR);
            $statement->execute();
        } catch (Exception $e) {
            $this->errors['username']['exists'] = "Something went wrong verifying your username.";
        }
        $result = $statement->fetch();
        if (!empty($result)) {
            $this->errors['username']['exists'] = "This username is already taken.";
            return false;
        } else {
            return true;
        }
    }


    /**
    * Checks if a email already exists in the database, @return bool true if passes
    * @return bool false on fail, adds errors to error property
    * Receives @param PDO connection
    */
    public function emailExists($email, PDO $db)
    {
        try {
            $query = "SELECT email FROM users WHERE email = ?";
            $statement = $db->prepare($query);
            $statement->bindParam(1, $email);
            $statement->execute();
        } catch (Exception $e) {
            $this->errors['email']['exists'] = "Something went wrong verifying your email.";
        }
        $result = $statement->fetch();
        if (!empty($result)) {
            $this->errors['email']['exists'] = "An account with this email already exists.";
            return false;
        } else {
            return true;
        }
    }


    /**
    * Requires @param parsedBody referring to a post array
    * Also requires @param rules which must be an associative array of
    * rules following the form of the Respect library
    * E.g., ['username' => v::alnum()->noWhiteSpace->isStringType()]
    * keys in rules array must match names of parsedBody keys
    * Password confirmation run as private method because the rule cannot be set
    * in a foreach loop
    */
    public function validate(Array $parsedBody, Array $rules)
    {
        foreach ($rules as $key => $value) {
            try {
                $value->assert($parsedBody[$key]);
            } catch (NestedValidationException $e) {
                $this->errors[$key] = $e->getMessages();
            }
        }
        $this->validatePassconfirm($parsedBody['password'], $parsedBody['passconfirm']);
        $this->addSessionErrors();
    }

    private function validatePassconfirm($pass, $passconfirm)
    {
        try {
            v::identical($pass)->assert($passconfirm);
        } catch (NestedValidationException $e) {
            $this->errors['passconfirm'] = "Passwords do not match.";
        }
    }
}
