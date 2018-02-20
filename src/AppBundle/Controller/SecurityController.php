<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $loginFbUrl = $this->container->get('socials.call')->createFbLink();
        //add the loginUrl to our array
        $data['loginFbUrl'] = $loginFbUrl;

        //google login
        $loginGoogUrl = $this->container->get('socials.call')->createGoogLink();
        //add the loginUrl to our array
        $data['loginGoogUrl'] = $loginGoogUrl;

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

    /**
     * @Route("/login/check-google", name="check_google")
     * @Method({"GET", "POST"})
     */
    public function checkGoogleAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
    
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
}