<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Articles;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ArticleEditorController extends Controller
{
    /**
     * @Route("/edit", name="article_editor")
     */
    public function indexAction(Request $request)
    {
        $article = new Articles();
        $form = $this->buildEditorForm($article);
    
        $form->handleRequest($request);
        
         if ($form->isSubmitted() && $form->isValid()) {
            $this->saveArticle($article);
         }
         
        $list = $this->getArticleList();
        return $this->render('editor.html.twig', array(
            'form' => $form->createView(),
            'list' => $list,
        ));
    }
    
    /**
     * @param Article $article 
     */
    public function buildEditorForm($article)
    {
        $form = $this->createFormBuilder($article)
            ->add('name', TextType::class)
            ->add('price', MoneyType::class)
            ->add('quantity', IntegerType::class, array('attr' => array('type' => 'number', 'min' => '0')))
            ->add('add', SubmitType::class, array('label' => 'Artikel hinzufügen'))
            ->getForm();
            
        return $form;
    }
    
    private function getArticleList()
    {
        $em = $this->getDoctrine()->getManager();
        $response = $em->getRepository('AppBundle:Articles')->findAll();

        return $response;
    }
    
    private function saveArticle($article)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('AppBundle:Articles')->findOneByName($article->getName());
        if(null !== $item)
        {
            $item->setPrice($article->getPrice());
            $item->setQuantity($article->getQuantity());
        } else {
            $em->persist($article);
        }
        
        $em->flush();
    }
    
    /**
     * @Route("/delete", name="article_editor_delete")
     */
    public function deleteArticle(Request $request)
    {
        if ($request->isXMLHttpRequest()) {         
            $id = $request->request->get('id');  
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Articles')->findOneById($id);    
            $em->remove($entity);
            $em->flush();  
            
            $response = array("id" => $id);
            return new JsonResponse($response);
        }
        
        //not an ajax call
        return new Response('This is not ajax!', 400);
    }
}