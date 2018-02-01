<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use\Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/addimage-browser", name="addimgbrowser")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addbrowserAction(Request $request)
    {
        $images = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image')->findAll();
        
        $form = $this->container->get('formcall.manager')
                     ->addForm($image = new Image(),$type = ImageType::class,$request, $typeMsg = 'notice',$msg = 'Image bien enregistré.', $url = $this->generateUrl('addimgbrowser'));
        if(!$form){return $this->redirectToRoute('addimgbrowser');}
        return $this->render('imgbrowser/addbrowser.html.twig',
        array('images' => $images,'formimg' => $form->createView(),));
    }

    /**
     * @Route("/image-import-ajax", name="imgimportajax")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAjaxInBrowserAction(Request $request)
    {
        $image = new Image();
        $form = $this->container->get('formcall.manager')
                     ->addAjaxForm($image ,$type = ImageType::class,$url = $this->generateUrl('imgimportajax'),$request, $typeMsg = 'notice',$msg = 'Image bien enregistré.');
        if(!$form){return $this->redirectToRoute('article_new');}
        
        return new JsonResponse(array(
            'message' => 'Error',
            'formimg' => $this->renderView('imgbrowser/form.html.twig',array(
                'image' => $image,
                'formimg' => $form->createView(),
                )
            )),400);
    }
}
