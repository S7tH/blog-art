<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use AppBundle\Entity\Contact;


class ContactMailer
{
    private $formfactory;
    private $mailer;

    public function __construct($formfactory,\Swift_Mailer $mailer)
    {
        $this->formfactory = $formfactory;
        $this->mailer = $mailer;
    }
    
    public function sendContactMail(Contact $contact, Request $request)
    {
        $formBuilder = $this->formfactory->createBuilder(FormType::class, $contact);
       
        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('fromEmail', EmailType::class)
            ->add('subject', TextType::class)
            ->add('body', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Enregistrer'));
        $form = $formBuilder->getForm();
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $message = (new \Swift_Message())
                ->setSubject($contact->getSubject())
                ->setFrom($contact->getFromEmail())
                ->setTo($contact->getToEmail())
                ->setBody($contact->geTbody());
                
            $send = $this->mailer->send($message);

            if($send === false){
                return $response = false;
            }
            else{
                return $response = 'success';
            }
            
        }
        return $form;
    }

}

