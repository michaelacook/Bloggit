<?php namespace Bloggit\Controllers;


/**
* Controller for signup page
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use Bloggit\User;
use Bloggit\Repositories\UserRepository;
use Respect\Validation\Validator as v;
use Bloggit\Validation\SignupValidator;
use Bloggit\Activation\Activator;
use Bloggit\Flash\FlashMessage as Flash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Bloggit\Email\Emailer;



class SignupController extends Controller
{

    public function getPage($request, $response, $args)
    {
        $args['auth'] = false;

        $args['pageStatus'] = 'active';
        $this->container->get('logger')->info("Sign up");

        if ($request->isPost()) {
            // Get data from post body
            $postData = $request->getParsedBody();
            $username = filter_var($postData['username'], FILTER_SANITIZE_STRING);
            $pass = filter_var($postData['password'], FILTER_SANITIZE_STRING);
            $passConfirm = filter_var($postData['confirm_password'], FILTER_SANITIZE_STRING);
            $email = filter_var($postData['email'], FILTER_SANITIZE_EMAIL);

            // validation checks, redirect on any fail
            $validator = new \Bloggit\Validation\SignupValidator();
            $validator->validate($postData, [
                'username' => v::alnum()->stringType()->noWhiteSpace(),
                'email' => v::email()->noWhiteSpace(),
                'password' => v::alnum()->stringType()->noWhiteSpace()
            ]);
            $validator->usernameExists($username, $this->container->get('db'));
            $validator->emailExists($email, $this->container->get('db'));
            if ($validator->checkValidationFail()) {
                return $response->withRedirect('/signup?signup=fail');
            } else {
                $token = Activator::generateToken();
                $user = new \Bloggit\User($username, $email, $pass, null, null, $token);
                if ($user->register(new \Bloggit\Repositories\UserRepository($this->container->get('db')))) {
                    $link = Activator::generateActivationLink('localhost', $token, $username);
                    $emailer = new \Bloggit\Email\Emailer(new \PHPMailer\PHPMailer\PHPMailer(true));
                    $emailer->setSubject("Activate Your Account");
                    $emailer->addRecipient([
                        'name' => $username,
                        'address' => $email
                    ]);
                    $emailer->setBody(
                        "<h3>Activate Your Account</h3><br />
                        <p>Hey there, " . $username . "! Thanks for registering on Bloggit!
                        Click the link below to activate your account and start blogging!</p><br />"
                        . $link
                    );
                    $emailer->sendEmail();
                    $flash = new Flash('success', 'Awesome! You will receive a confirmation email shortly.');
                    return $response->withRedirect('/signup?signup=success');
                }
            }
        } else if ($request->isGet()) {
            return $this->view->render($response, 'signup.twig', $args);
        }
    }
}
