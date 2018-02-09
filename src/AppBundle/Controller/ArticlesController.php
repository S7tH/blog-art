<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use AppBundle\Entity\Commentary;
use AppBundle\Form\Type\CommentaryType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ArticlesController extends Controller
{
    /**
     * @Route("/article/{id}", name="article")
     * @Method({"GET", "POST"})
     */
    public function viewAction(Article $article, Request $request)
    {
        $serviceCom = $this->container->get('app.commentaries');
        $commentaries = $serviceCom->viewcom($article);

        //we recover the user instance for our commentary by $this->getUser()
        $commentary = $serviceCom->addcom($article, $this->getUser());

        $form = $this->container->get('formcall.manager')
                     ->addForm($commentary ,$type = CommentaryType::class,$request, $typeMsg = 'com',$msg = 'Votre commentaire, a bien été enregistré.');
        if(!$form){return $this->redirect($this->generateUrl('article', array('id' => $article->getId() )));}
    
        return $this->render('articles/view.html.twig', array(
            'article' => $article,
            'id' => $article->getId(),
            'commentaries' => $commentaries,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/article-new", name="article_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction(Request $request)
    {
        $form = $this->container->get('formcall.manager')
                     ->addForm($article = new Article(),$type = ArticleType::class,$request, $typeMsg = 'notice',$msg = 'Article bien enregistré.');
        if(!$form){return $this->redirectToRoute('homepage');}
        
        return $this->render('articles/add.html.twig',array(
                'form' => $form->createView(),));
    }

    /**
     * @Route("/article-edit/{id}", name="article_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Article $article, Request $request )
    {
        $form = $this->container->get('formcall.manager')
                     ->addForm($article ,$type = ArticleType::class,$request, $typeMsg = 'notice',$msg = 'Article bien modifié et enregistré.');
        if(!$form){return $this->redirectToRoute('homepage');}

        return $this->render('articles/edit.html.twig',
        array('form' => $form->createView(),));
    }

    /**
     * @Route("/commentary-edit/{id}/{article}", name="commentary_edit")
     * @Method({"GET", "POST"})
     */
    public function comEditAction(Commentary $commentary, $article, Request $request )
    {
        $form = $this->container->get('formcall.manager')
                     ->addForm($commentary ,$type = CommentaryType::class,$request, $typeMsg = 'com',$msg = 'Votre commentaire, a bien été modifié.');
        if(!$form){return $this->redirectToRoute('article',array('id' => $article));}

        return $this->render('commentaries/edit.html.twig',
        array('form' => $form->createView(),));
    }

    /**
     * @Route("/commentary-delete/{id}/{article}", name="commentary_delete")
     * @Method({"GET", "DELETE"})
     */
    public function comDeleteAction(Commentary $commentary, $article, Request $request)
    {
        $this->container->get('app.save')->contentDelete($commentary);//delete the article
        $request->getSession()->getFlashBag()->add('notice', 'Votre commentaire a bien été supprimé.');
        return $this->redirectToRoute('article',array(
            'id' => $article
            )
        );
    }

    /**
     * @Route("/article-delete/{id}", name="article_delete")
     * @Method({"GET", "DELETE"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Article $article, Request $request)
    {
        $service = $this->container->get('app.save');
        $service->comsCheckDelete($article);//delete all article's commentaries
        $service->contentDelete($article);//delete the article
        $request->getSession()->getFlashBag()->add('notice', 'Article bien supprimé.');
        return $this->redirectToRoute('homepage');// We are displaying now the homepage page thanks a redirection to its route.
    }
}
