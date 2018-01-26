<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FacebookCall
{
    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //create the link access to the facebook login
    public function createFbLink($clientId, $clientSecret, $redirectUrl)
    {
        $fb = new \Facebook\Facebook([
            "app_id" => $clientId,"app_secret" => $clientSecret]
        );

        $helper = $fb->getRedirectLoginHelper();// to set redirection url

        $permissions = ["email"];// set required permissions to user details
    
        $loginUrl = $helper->getLoginUrl($redirectUrl , $permissions);

        return $loginUrl;
    }

    public function redirectionFb($clientId, $clientSecret, $userManager)
    {
        $fb = new \Facebook\Facebook([
            "app_id" => $clientId,"app_secret" => $clientSecret]
        );
    
        $helper = $fb->getRedirectLoginHelper();// to perform operation after redirection
        try
        {
            $accessToken = $helper->getAccessToken();// to fetch access token
        }
        catch(Facebook\Exceptions\FacebookResponseException $e)
        {
            // When facebook server returns error
            echo "Graph returned an error: " . $e->getMessage();
            exit;
        }
        catch(Facebook\Exceptions\FacebookSDKException $e)
        {
            // when issue with the fetching access token
            echo "Facebook SDK returned an error: " . $e->getMessage();
            exit;
        }
        if (! isset($accessToken))// checks whether access token is in there or not
        {
            if ($helper->getError())
            {
                header("HTTP/1.0 401 Unauthorized");
                echo 'Error: ' . $helper->getError() . '\n';
                echo 'Error Code: ' . $helper->getErrorCode() . '\n';
                echo 'Error Reason: ' . $helper->getErrorReason() . '\n';
                echo 'Error Description: ' . $helper->getErrorDescription() . '\n';
            }
            else
            {
                header("HTTP/1.0 400 Bad Request");
                echo "Bad request";
            }
        exit;
        }
        try
        {
            // to get required fields using access token
            $response = $fb->get("/me?fields=id,name,email", $accessToken->getValue());
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

        $userMail = $userData['email'];

        //check if this user exist in database
        $user = $userManager->findUserBy(['email' => $userMail]);

        if(!$user)
        {
            $ContentString = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTwWxXyYzZ0123456789';
            $mix = str_shuffle($ContentString);
            $password = hash('sha256', substr($mix,0,10));
            $salt = hash('sha256', substr($mix,0,42));

            $user->setUsername($userData['name']);
            $user->setUsernameCanonical(strtoupper($userData['name']));
            $user->setEmail($userData['email']);
            $user->setEmailCanonical(strtoupper($userData['email']));
            $user->setSalt($salt);
            $user->setEnabled(true);
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $userManager->updateUser($user);
        }
        return $user;
        //return $userData;
    }
}




