<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(['/cart'])]
class CartController extends AbstractController
{
    #[Route('/{product}/add', name: 'cart_add')]
    public function add(Product $product, UserRepository $userRep, SessionInterface $session): Response
    {
        /*return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);*/

        $user = $this->getUser();

        $cart = $session->get('cart', []);

        $id = $product->getId();
        if (array_key_exists($product->getId(), $cart)){
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $session->set('cart', $cart);
        /*[
            281 => 1,
            302 => 2,
        ];*/

        return $this->redirectToRoute('cart_show', [
            'id' => $id,
            'user' => $user,
        ]);
       
    }

     #[Route('/{product}/less', name: 'cart_less')]
    public function less(Product $product, UserRepository $userRep, SessionInterface $session): Response
    {
        $user = $this->getUser();

        $cart = $session->get('cart', []);

        $id = $product->getId();
        if (1 == $cart[$id]) {
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('cart_show', [
            'id' => $id,
            'user' => $user,
        ]);
       
    }

     #[Route('/{product}/remove', name: 'cart_remove')]
    public function remove(Product $product, UserRepository $userRep, SessionInterface $session): Response
    {
    
        $user = $this->getUser();
        
        $cart = $session->get('cart', []);

        $id = $product->getId();
        unset($cart[$id]);
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('cart_show', [
            'id' => $id,
            'user' => $user,
        ]);
       
    }

    #[Route('/show', name: 'cart_show')]
    public function show(SessionInterface $session, UserRepository $userRep, ProductRepository $productRep): Response
    {
        $user = $this->getUser();
        
        $fullCart = [];
        $total = 0;
        $cart = $session->get('cart', []);

        foreach ($cart as $id => $qty) {
            $product = $productRep->find($id);
            $fullCart[] = [
                'product' => $product,
                'qty'=> $qty,
            ];

            $total += $product->getPrice() * $qty;
        }
        //dd($fullCart);
        return $this->render('cart/show.html.twig', [
            'cartProducts' => $fullCart,
            'total' => $total,
            'user' => $user,
        ]);

    }
}
