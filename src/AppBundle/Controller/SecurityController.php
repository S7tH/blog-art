<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use AppBundle\Entity\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use FOS\UserBundle\Controller\SecurityController as BaseSecurity;

class SecurityController extends BaseSecurity
{        
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        //facebook login
        $clientId = $this->container->getParameter('facebook_client_id');
        $clientSecret = $this->container->getParameter('facebook_client_secret');
        $redirectUrl = $this->generateUrl('check_facebook', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $loginUrl = $this->container->get('facebook.call')->createFbLink($clientId, $clientSecret, $redirectUrl);
        //end facebook
        //add the loginUrl to our array
        $data['loginUrl'] = $loginUrl;

        return $this->render('@FOSUser/Security/login.html.twig', $data);
    }

    
    /**
     * @Route("/login/check-facebook", name="check_facebook")
     * @Method({"GET", "POST"})
     */
    public function checkFacebookAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
    
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
}
