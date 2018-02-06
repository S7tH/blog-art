<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\ImageType;
use Symfony\Component\HttpFoundation\Request;
use\Symfony\Component\HttpFoundation\JsonResponse;


class FormCallManager
{
    private $formfactory;
    private $save;
    

    public function __construct($formfactory, $save)
    {
        $this->formfactory = $formfactory;
        $this->save = $save;

    }

    public function addForm($object,$type, Request $request, $typeMsg, $msg, $url = null, $method = null, $target =null)
    {
        if($url === null)
        {
            $form = $this->formfactory->create($type, $object);
        }
        else if($target === null)
        {
            $form = $this->formfactory->create($type, $object,array(
                'action' => $url,
                'method' => $method));
        }
        else
        {
            $form = $this->formfactory->create($type, $object,array(
                'action' => $url,
                'method' => $method,
                'attr' => ['target' => $target]));
        }
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $this->save->contentSave($object);//save the form content
            $request->getSession()->getFlashBag()->add($typeMsg, $msg);
            return false;
        }
        return $form;
    }


    public function addAjaxForm($object,$type,$url, Request $request, $typeMsg, $msg)
    {
        $form = $this->createAjaxForm($object,$type,$url);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $this->save->contentSave($object);//save the form content
            $request->getSession()->getFlashBag()->add($typeMsg, $msg);
            return new JsonResponse(array('message' => 'Success!'), 200);
        }
        return $form;
    }
}
