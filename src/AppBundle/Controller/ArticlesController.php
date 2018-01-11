<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;

class ArticlesController extends Controller
{
    /**
     * @Route("/article/{id}", name="article")
     * @Method({"GET"})
     */
    public function viewAction(Article $article, Request $request)
    {
        return $this->render('articles/view.html.twig', array(
            'article' => $article,
            'id' => $article->getId(),
        ));
    }

    /**
     * @Route("/article-new", name="article_new")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        $article = new Article();
        $form = $this->get('form.factory')->create(ArticleType::class, $article);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistré.');
                return $this->redirectToRoute('homepage');
        }
        return $this->render('articles/add.html.twig',
        array(
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/article-edit/{id}", name="article_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Article $article, Request $request )
    {
        //recover entity manager
        $em = $this->getDoctrine()->getManager();
        //create the form
        $form = $this->get('form.factory')->create(ArticleType::class, $article);
        
        //if a form has been send so we are not displaying the form but send the form and if the values are ok
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em->persist($article);//create the request sql
            $em->flush();//send the request and save in the db
            $request->getSession()->getFlashBag()->add('notice', 'Article bien modifié et enregistré.');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('articles/edit.html.twig',
        array(
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/article-delete/{id}/{state}", name="article_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Article $article, $state, Request $request)
    {
        if($state == 'confirm')
        {
            return $this->render('articles/delete.html.twig',
             array(
                'article' => $article
            ));
        }
        else
        {
            //recover the entity manager
            $em = $this->getDoctrine()->getManager();

            //we delete our entity from the db
            $em->remove($article);//create the request sql for deleting
            $em->flush();//send the request and delete our object in the db

            $request->getSession()->getFlashBag()->add('notice', 'Trick bien supprimé.');

            // We are displaying now the homepage page thanks a redirection to its route.
            return $this->redirectToRoute('homepage');
        }
    }
}
