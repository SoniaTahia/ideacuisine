<?php

namespace App\Controller;

use DateTime;
use App\Entity\Invoice;
use App\Entity\Purchase;
use App\Form\InvoiceType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'invoice')]
    public function index(): Response
    {

        // compte stripe

        // récupérer les données de livraison

        // créer les données dans la BDD

        // faire payer

        // vider le panier et remercier
        return $this->render('invoice/index.html.twig', [
            'controller_name' => 'InvoiceController',
        ]);
    }

    #[Route('/new', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, ProductRepository $productRep): Response
    {
        $invoice = new Invoice();
        $user = $this->getUser();
        if ($user) {
            $invoice->setName($user->getName())
                ->setFirstname($user->getFirstname());
        }
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
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $invoice->setPrice($total)
                    ->setPaid(false)
                    ->setStripeSuccessKey(uniqid())
                    ->setReference("")
                    ->setDate(new DateTime("now"));
            $entityManager->persist($invoice);
            foreach ($cart as $id => $qty) {
                $product = $productRep->find($id);
                $purchase = new Purchase;
                $purchase->setInvoice($invoice)
                    ->setProduct($product)
                    ->setUnitPrice($product->getPrice())
                    ->setQuantity($qty);
                $entityManager->persist($purchase);
            }

            $entityManager->flush();
                
            return $this->redirectToRoute('stripe_checkout', ["invoice" => $invoice->getId()], Response::HTTP_SEE_OTHER);
        }
       
        return $this->renderForm('invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'cartProducts' => $fullCart,
            'total' => $total,
        ]);
    }
}
