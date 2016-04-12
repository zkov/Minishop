<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Articles;

class ArticleListController extends Controller
{
    /**
     * @Route("/", name="list_page")
     */
    public function indexAction(Request $request)
    {
        $list = $this->getArticleList();
        
        return $this->render('articlelist.html.twig', array(
            'list' => $list,
        ));
    }
    
    private function getArticleList()
    {
        $em = $this->getDoctrine()->getManager();
        $response = $em->getRepository('AppBundle:Articles')->findAll();

        return $response;
    }
    
    /**
     * @Route("/addtocart", name="add to cart")
     */
     public function addAction(Request $request)
     {
         $id = null;
         $quantity = null;
         $cart = null;
        //  $session = $this->getRequest()->getSession();
         
        if ($request->isXMLHttpRequest()) {         
            $id = $request->request->get('id');  
            $name = $request->request->get('name');
            $price = $request->request->get('price');
            $quantity = $request->request->get('quantity');
            $sample_cart = array();   
            $sample_cart[$id] = array(
                    'name' => $name,
                    'price' => $price,
                    'ordered' => 1
                );
            
            if(null !== $this->get('session')->get('cart'))
            {
                $session_cart = $this->get('session')->get('cart');
                if(isset($session_cart[$id]))
                {
                    $session_cart[$id]['ordered'] = $session_cart[$id]['ordered'] + 1;
                } else {
                    $session_cart[$id] = $sample_cart[$id];
                }
                $this->get('session')->set('cart', $session_cart);
            } else {
                
                $this->get('session')->set('cart', $sample_cart);
            }
            
            // the returned current qunatity to the ajax after cart add
            $current_quantity = $quantity - 1;      
                   
            $response = array("id" => $id, "quantity" => $current_quantity);
            return new JsonResponse($response);
        }
        
        //not an ajax call
        return new Response('This is not ajax!', 400);
     }
}
