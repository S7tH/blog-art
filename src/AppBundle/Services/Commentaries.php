<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Commentary;

class Commentaries
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    //affiche les commentaires liés a l'article en cours
    public function viewcom($article)
    {
        return $this->em->getRepository('AppBundle:Commentary')->findByCom($article);
    }

    //affiche le formulaire pour ecrire un com et le valider
    public function addcom($article, $user)
    {
        //$article = $this->em->getRepository('AppBundle:Article')->findById($article);
        $commentary = null;

        if ($user !== null) //if any user is connected user is null and we don't build a form and don't display it in twig view
        {
            $commentary = new Commentary($article); 
            $commentary->setUser($user);
        }
        return $commentary;
    }
}
