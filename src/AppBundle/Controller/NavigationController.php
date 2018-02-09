<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Contact;


use AppBundle\Entity\Article;

class NavigationController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        //recover the repository
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Article');
    
        //recover all the entities
        $articlelist = $repository->articlelist();
        
        
        return $this->render('navigation/index.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'articlelist' => $articlelist,
        ));
    }

    /**
     * @Route("/gallerie", name="gallery")
     * @Method({"GET"})
     */
    public function galleryAction()
    {
        return $this->render('navigation/gallery.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     * @Method({"GET", "POST"})
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact($this->container->getParameter('mailer_user'));

        $form = $this->container->get('app.contact_mailer')
                    ->sendContactMail($contact,$request);
        if(!$form){return $this->redirectToRoute('homepage');}

        return $this->render('navigation/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
