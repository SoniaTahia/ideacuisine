<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Invoice;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/{invoice}/stripe', name: 'stripe_checkout')]
    public function checkout(Invoice $invoice, PurchaseRepository $purchaseRepo, Request $request, EntityManagerInterface $em): Response
    {
        

        $privateKey = "sk_test_51KaPpcBlfeiezpcIPppfNuuFW0T7bA1fmd4qOmsf4ybbLpTtnT0TnmKbCueodxWZRkqENvlct8LWsltbNTUw9Ov500gFi92ws4";
        Stripe::setApiKey($privateKey);

        $purchaseCriteria = [
            "invoice" => $invoice,
        ];
        $purchases = $purchaseRepo->findBy($purchaseCriteria);
        $lineItems = [];
        foreach ($purchases as $purchase) {
            $item = [
                "price_data" => [
                    "currency" => "eur",
                    "product_data" => [
                        "name" => $purchase->getProduct()->getName(),
                    ],
                    "unit_amount" => $purchase->getUnitPrice(),
                ],
                "quantity" => $purchase->getQuantity(),
            ];
            $lineItems[] = $item;
        }
        $successRoute = $this->generateUrl('stripe_valid_payment', [
            
            "invoice" => $invoice->getId(),
            "stripeSuccessKey" => $invoice->getStripeSuccessKey(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $errorRoute = $this->generateUrl('stripe_error_payment', [
            
            "invoice" => $invoice->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $stripeSession = \Stripe\Checkout\Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'success_url' => $successRoute,
            'cancel_url' => $errorRoute,
            //'user' => $user,
        ]);
        $invoice->setPiStripe($stripeSession->payment_intent);
        $em->flush($invoice);
        return $this->redirect($stripeSession->url, 303);

    }

    #[Route('/stripe/{invoice}/success/{stripeSuccessKey}', name: 'stripe_valid_payment')]
    public function success(Invoice $invoice, string $stripeSuccessKey, SessionInterface $session, PurchaseRepository $purchaseRepo): Response
    {
        $user = $this->getUser();

        //Trouver le code pour valider le success seulement si STRIPE confirme le paiement
        
        if ($stripeSuccessKey != $invoice->getStripeSuccessKey()) {
            $this->redirectToRoute("stripe_error_payment", [
                'invoice' => $invoice->getId(),
            ]);
        }
        $invoice->setPaid(true);
        $session->set('cart', []);
        $purchaseCriteria = [
            "invoice" => $invoice,
        ];
        $purchases = $purchaseRepo->findBy($purchaseCriteria);
        return $this->render('stripe/success.html.twig', [
            'invoice' => $invoice,
            'purchases' => $purchases,
            'user' => $user,
        ]);
    }
    
    #[Route('/stripe/{invoice}/cancel', name: 'stripe_error_payment')]
    public function error(Invoice $invoice): Response
    {
        $this->addFlash("Erreur dans le process de paiement");
        return $this->redirectToRoute("cart_show");
    }
}
