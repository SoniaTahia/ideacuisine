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
use App\Entity\User;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
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
        $user = $this->getUser();

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
                  ]);

                  if ($contactFile = [] ){
                        $email->attachFromPath($projectPath . 'upload/'. $contact->getFile());
                        $upLoadService->delete($contact->getFile());
                  }
                  $mailer->send($email);
            
            $this->addFlash("success", "Votre message a bien ??t?? envoy??");
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('home/contact_user.html.twig', [
            'contact' => $contact,
            'form' => $form,
            'user' => $user,
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
            'user' => $user,
        ]);
    }

    #[Route('/condition', name: 'home_condition')]
    public function condition(): Response
    {
            return $this->render('home/condition.html.twig', [
            'controller_name' => 'HomeController',          
        ]);
    }

    #[Route('/mention', name: 'home_mention')]
    public function mention(): Response
    {
            return $this->render('home/mention.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/policy', name: 'home_policy')]
    public function policy(): Response
    {
            return $this->render('home/policy.html.twig', [
            'controller_name' => 'HomeController',
          
        ]);
    }

    
    
}
