<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;


class DBSave
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    //sauvegarde le contenu du formulaire 
    public function contentSave($content)
    {
        $this->em->persist($content);//create the request sql
        $this->em->flush();//send the request and save in the db
    }

    //check et efface les commentaires existant sur l'article a effacé 
    public function comsCheckDelete($article)
    {      
        //we recover and check if the trick is linked to commentaries  
        $comments = $this->em->getRepository('AppBundle:Commentary')->findBy(array('article' => $article));
        
        foreach($comments as $comment)
        {
            // if the article_id exist
            if(null !== $comment)
            {
                $this->em->remove($comment);//create the request sql for deleting
            }
        }
    }

    //effacer un objet de la db
    public function contentDelete($content)
    {
        $this->em->remove($content);//create the request sql for deleting
        $this->em->flush();//send the request and delete our object in the db
    }
}
