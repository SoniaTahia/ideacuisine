<?php

namespace App\Controller;

use DateTime;

use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Form\InvoiceType;
use App\Repository\UserRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'invoice_index')]
    public function index(InvoiceRepository $invoiceRep): Response
    {

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoiceRep->findAll(),
        ]);
    }

    #[Route('/{id}/show', name: 'invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice, Product $product, User $user): Response
    {
        $user = $this->getUser();

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
            'product' => $product,
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, ProductRepository $productRep): Response
    {
       
        $user = $this->getUser();

        $invoice = new Invoice();
        
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
                    ->setQuantity($qty)
                    ->setInvoiceUser($user->getInvoiceUser());
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

    #[Route('/{id}/edit', name: 'invoice_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'user' =>$user,
        ]);
    }

    #[Route('/{id}/delete', name: 'invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('invoice_index', [], Response::HTTP_SEE_OTHER);
    }
   
    #[Route('/{id}/user_invoice', name: 'invoice_user_invoice', methods: ['GET'])]
    public function userInvoice(InvoiceRepository $invoiceRep, User $user): Response
    {
        $user = $this->getUser();

        return $this->render('invoice/user_invoice.html.twig', [
            'invoices' => $invoiceRep->findAll(),
            'user' => $user,
        ]);
    }
}
