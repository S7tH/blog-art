<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SocialsCall
{
    private $router;
    private $fbId;
    private $fbSecret;
    private $googId;
    private $googSecret;

    public function __construct(Router $router, $fbId, $fbSecret, $googId, $googSecret)
    {
        $this->router = $router;
        $this->fbId = $fbId;
        $this->fbSecret = $fbSecret;
        $this->googId = $googId;
        $this->googSecret = $googSecret;
    }

    //create the link access to the facebook login
    public function createFbLink()
    {
        $fb = new \Facebook\Facebook([
            "app_id" => $this->fbId,"app_secret" => $this->fbSecret]
        );

        $helper = $fb->getRedirectLoginHelper();// to set redirection url

        $permissions = ["email"];// set required permissions to user details

        $redirectUri = $this->router->generate('check_facebook', [], ROUTER::ABSOLUTE_URL);
    
        $loginFbUrl = $helper->getLoginUrl($redirectUri , $permissions);

        return $loginFbUrl;
    }

    //create the link access to the google login
    public function createGoogLink()
    {
        $client= new \Google_Client();
        $client->setApplicationName("eloine");// to set app name
        $client->setClientId($this->googId);// to set app id or client id
        $client->setClientSecret($this->googSecret);// to set app secret or client secret
        $redirectUri = $this->router->generate('check_google', [], ROUTER::ABSOLUTE_URL);
        $client->setRedirectUri($redirectUri);// to set redirect uri
        $client->setScopes(["email", "profile"]);
        $loginGoogUrl= $client->createAuthUrl();// to get login url
        return $loginGoogUrl;
    }

    
    public function fbProfil(UserManagerInterface $userManager, UserResponseInterface $response)
    {
        $fb = new \Facebook\Facebook([
            "app_id" => $this->fbId, "app_secret" => $this->fbSecret]
        );
    
        try
        {
            // to get required fields using access token
            $response = $fb->get("/me?fields=id,first_name,email", $response->getAccessToken());
        }
        catch(Facebook\Exceptions\FacebookResponseException $e)// throws an error if invalid fields are specified
        {
            echo "Graph returned an error: " . $e->getMessage();
            exit;
        }
        catch(Facebook\Exceptions\FacebookSDKException $e)
        {
            echo "Facebook SDK returned an error: " . $e->getMessage();
            exit;
        }

        $userData = $response->getGraphUser();// to get user details
        //var_dump($userData);die; //to see the content of this var
        $userMail = $userData['email'];

        //check if this user exist in database
        $user = $userManager->findUserBy(['email' => $userMail]);

        return static::userExistence($response, $userManager, $user, $userData, $userData['first_name'], "Facebook");
    }

    public function googProfil(UserManagerInterface $userManager, UserResponseInterface $response)
    {
        $client= new \Google_Client();
        $client->setApplicationName("eloine");// to set app name
        $client->setClientId($this->googId);// to set app id or client id
        $client->setClientSecret($this->googSecret);// to set app secret or client secret
        $redirectUri = $this->router->generate('check_google', [], ROUTER::ABSOLUTE_URL);
        $client->setRedirectUri($redirectUri);// to set redirect uri
        $service = new \Google_Service_Oauth2($client);
        $client->setAccessToken($response->getAccessToken());// to get access token
        $userData=$service->userinfo->get();// to get user detail by using access token
        //var_dump($userData);die; //to see the content of this var

        $userMail = $userData['email'];

        //check if this user exist in database
        $user = $userManager->findUserBy(['email' => $userMail]);

        return static::userExistence($response, $userManager, $user, $userData, $userData['givenName'], "Google");
    }

    public function userExistence($response, UserManagerInterface $userManager,$user, $userData, $userNick, $social)
    {
        $setter = 'set'.$social;
        $setterId = $setter.'Id';
        $setterToken = $setter.'AccessToken';
        $getter = 'get'.$social;
        $getterId = $getter.'Id';

        if(!$user)
        {
            $ContentString = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTwWxXyYzZ0123456789';
            $mix = str_shuffle($ContentString);
            $password = hash('sha256', substr($mix,0,10));
            $salt = hash('sha256', substr($mix,0,42));
        
            $user = $userManager->createUser();
            $user->setUsername($userNick);
            $user->setUsernameCanonical(strtoupper($userNick));
            $user->setEmail($userData['email']);
            $user->setEmailCanonical(strtoupper($userData['email']));
            $user->setSalt($salt);
            $user->setEnabled(true);
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $user->$setterId($userData['id']);
            $user->$setterToken($response->getAccessToken());

            $userManager->updateUser($user);
            return $user;
        }
        //if user exists, update access token
        if(empty($user->$getterId()))
        {
            $user->$setterId($userData['id']);
        }
        $user->$setterToken($response->getAccessToken());
        $userManager->updateUser($user);

        return $user;
    }
}
