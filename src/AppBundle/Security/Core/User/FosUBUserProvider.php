<?php

namespace AppBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class FosUBUserProvider extends BaseFOSUBProvider
{
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        // get property from provider configuration by provider name
        // , it will return `facebook_id` in that case (see service definition below)
        $property = $this->getProperty($response);//"facebook_id"
        $username = $response->getUsername(); // get the unique facebook user identifier
       
        
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();//facebook
        $setter = 'set'.ucfirst($service);//setFacebook
        $setter_id = $setter.'Id';//setFacebookId
        $setter_token = $setter.'AccessToken';//setFacebookAccessToken

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) { //if facebook_id $username exist
            $previousUser->$setter_id(null);//setFacebookId(null)
            $previousUser->$setter_token(null);//setFacebookAccessToken(null)
            $this->userManager->updateUser($previousUser);
        }
        //we connect current user
        $user->$setter_id($username);//setFacebookId(facebook_id)
        $user->$setter_token($response->getAccessToken());//setFacebookAccessToken(current token)
        
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
       
        $username = $response->getUsername();
        $userMail = $response->getEmail();
        $userNick = $response->getFirstName();

        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username)); //facebook_id => facebook_id

        //when the user is registrating
        if (null === $user) { // if any object with this facebook_id has not found, then we create this one
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);//setFacebook
            $setter_id = $setter.'Id';//setFacebookId
            $setter_token = $setter.'AccessToken';//setFacebookAccessToken
            
            $checkUser = $this->userManager->findUserBy(['email' => $userMail]);
            if($checkUser)
            {
                $checkUser->$setter_id($username);
                $checkUser->$setter_token($response->getAccessToken());
                $this->userManager->updateUser($checkUser);
                return $checkUser;
            }
            else
            {        
                $ContentString = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTwWxXyYzZ0123456789';
                $mix = str_shuffle($ContentString);
                $password = hash('sha256', substr($mix,0,10));
                $salt = hash('sha256', substr($mix,0,42));
                // create new user here
                $user = $this->userManager->createUser();
                $user->setUsername($userNick);
                $user->setUsernameCanonical(strtoupper($userNick));
                $user->setEmail($userMail);
                $user->setEmailCanonical(strtoupper($userMail));
                $user->setSalt($salt);
                $user->setEnabled(true);
                $user->setPassword($password);
                $user->setRoles(['ROLE_USER']);

                $user->$setter_id($username);
                $user->$setter_token($response->getAccessToken());
            
                $this->userManager->updateUser($user);
                return $user;
            }   
        }
        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        //update access token
        $user->$setter($response->getAccessToken());
        return $user;
    }
}