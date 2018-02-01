<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Image;
use AppBundle\Form\Type\ImageType;

class ImageBrowserController extends Controller
{
    /**
     * @Route("/image-browser", name="imgbrowser")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function displayBrowserAction(Request $request)
    {
        $images = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image')->findAll();
        
        return $this->render('imgbrowser/imgbrowser.html.twig',
        array('images' => $images,));
    }

    /**
     * @Route("/image-import", name="imgimport")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addInBrowserAction(Request $request)
    {
        $form = $this->container->get('formcall.manager')
                     ->addForm($image = new Image() ,$type = ImageType::class,$request, $typeMsg = 'notice',$msg = 'Image bien enregistré.');
        if(!$form){return $this->redirectToRoute('imgbrowser');}

        return $this->render('imgbrowser/form.html.twig',
        array('form' => $form->createView(),));
    }
}
