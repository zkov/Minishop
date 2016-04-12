<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * @Route("/warenkorb", name="cart_page")
     */
    public function indexAction(Request $request)
    {
        //$session = new Session();
        $cart = $this->get('session')->get('cart');
        
        if(null === $cart) {
            return new Response("Warenkord ist leer");
        }
        
        return $this->render('cart.html.twig', array(
            'list' => $cart
        )
        );
    }
    
    /**
     * @Route("/clear", name="clear_cart")
     */
    public function clearCartAction(Request $request)
    {
        $this->get('session')->invalidate();
        
        $message = "Warenkorb is leer";
        return new Response($message);
    }
    
    /**
     * @Route("/order", name="order_cart")
     */
    public function orderCartAction()
    {
        $cart = $this->get('session')->get('cart');
        foreach ($cart as $key => $value) {
            $this->setDbItemQuantity($key, $cart[$key]['ordered']);
        }
        $message = "Die Artiklen sind bestellt";
        return new Response($message);
    }
    
    private function setDbItemQuantity($id, $ordered)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('AppBundle:Articles')->find($id);
        $quantity = $item->getQuantity() - $ordered;
        $item->setQuantity($quantity);
        // call to flush that entity manager from which we create $item
        $em->flush();
    } 
    
}
