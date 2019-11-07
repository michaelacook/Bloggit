<?php namespace Bloggit\Activation;


/**
* Email activation class. Provides static methods for account activation via email link
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use PDO;


class Activator
{

    public static function generateToken(int $tokenLength=20)
    {
        $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXXYZ_-';
        $token = '';
        for ($i = 0; $i <= $tokenLength; $i++) {
            $token .= $charset[rand(0, strlen($charset)) - 1];
        }
        return $token;
    }

    public static function generateActivationLink($domain, $token, $user)
    {
        $uri =  $domain . '/login?user=' . $user . '&t=' . $token;
        return '<a href="' . $uri . '">Activate Account</a>';
    }

    public static function activate($token, $username, PDO $db)
    {
        try {
            $query = "SELECT activation_token FROM users WHERE username = ? AND activation_token = ?";
            $statement = $db->prepare($query);
            $statement->bindParam(1, $username);
            $statement->bindParam(2, $token);
            $statement->execute();
        } catch (Exception $e) {
            return false;
        }
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result['activation_token'] === $token) {
            $query = "UPDATE users SET account_status = 'active' WHERE username = ? AND activation_token = ?";
            $statement = $db->prepare($query);
            $statement->bindParam(1, $username, PDO::PARAM_STR);
            $statement->bindParam(2, $token, PDO::PARAM_STR);
            if ($statement->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
