<?php

namespace AppBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use AppBundle\Services\SocialsCall;


class FosUBUserProvider extends BaseFOSUBProvider
{
    /**
     * @var SocialsCall
     */
    protected $socials;
    

    public function __construct(UserManagerInterface $userManager, array $properties, SocialsCall $socials)
    {
        $this->userManager = $userManager;
        $this->properties = array_merge($this->properties, $properties);
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->socials = $socials;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        // get property from provider configuration by provider name
        // , it will return `facebook_id` in that case (see service definition below)
        $property = $this->getProperty($response);
        $username = $response->getUsername(); // get the unique social user identifier
       
        
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setterId = $setter.'Id';
        $setterToken = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) { //if facebook_id or google_id => $username exist
            $previousUser->$setterId(null);
            $previousUser->$setterToken(null);
            $this->userManager->updateUser($previousUser);
        }
        //we connect current user
        $user->$setterId($username);
        $user->$setterToken($response->getAccessToken());
        
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $service = $response->getResourceOwner()->getName();

        if($service === "facebook")
        {
            //facebook user
            return $user = $this->socials->fbProfil($this->userManager, $response);
        }
        else
        {
            //google user
            return $user = $this->socials->googProfil($this->userManager, $response);
        }
    }
}
