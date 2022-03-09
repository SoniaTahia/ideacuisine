<?php

namespace App\Controller;

use DateTime;
use App\Tool\DateTool;
use App\Entity\Category;
use App\Form\ContactType;
use App\Form\Builder\Contact;
use App\Service\UploadService;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/contact_info', name: 'home_contact_info')]
    public function contactInfo(): Response
    {
        return $this->render('home/contact_info.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact_user', name: 'home_contact_user')]
    public function contactUser(Request $request, MailerInterface $mailer, UploadService $upLoadService): Response
    {   
        
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
             
            $contactFile = $form->get('file')->getData();
            
            if ($contactFile) {
                $file = $upLoadService->upload($contactFile);
                $contact->setFile($file);
            }   
            $projectPath = $request->server->get("DOCUMENT_ROOT");
            
            $email = new TemplatedEmail();
            $email->to(new Address("admin@ideacuisine.com.tn", "Idea Cuisine"))
                  ->from($contact->getEmail())
                  ->subject($contact->getSubject())
                  ->htmlTemplate('email/contact.html.twig')
                  ->context([
                      "message" => $contact->getMessage(),
                  ])
                  ->attachFromPath($projectPath . 'upload/'. $contact->getFile()); 
                    
                  
                  $mailer->send($email);
            
            $this->addFlash("success", "Votre message a bien été envoyé");
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('home/contact_user.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/abc', name: 'home_new_cat')]
    public function newCat(CategoryRepository $categoryRepo, EntityManagerInterface $em): Response
    {
        $category = $categoryRepo->find(2);

        $category->setName("Cours Symfony Debutant");
        $em->flush(); 

        dd($category);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    
}
