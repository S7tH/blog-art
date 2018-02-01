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

    public function addForm($object,$type, Request $request, $typeMsg, $msg)
    {
        $form = $this->formfactory->create($type, $object);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $this->save->contentSave($object);//save the form content
            $request->getSession()->getFlashBag()->add($typeMsg, $msg);
            return false;
        }
        return $form;
    }
}
