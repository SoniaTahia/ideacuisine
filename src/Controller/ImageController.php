<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\Image1Type;
use App\Service\UploadService;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/image')]
class ImageController extends AbstractController
{
    #[Route('/', name: 'image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository): Response
    {
        $user = $this->getUser();

        return $this->render('image/index.html.twig', [
            'images' => $imageRepository->findAll(),
            'user' => $user,
        ]);
    }

    #[Route('/new/{product}', name: 'image_new', methods: ['GET', 'POST'])]
    public function new(Product $product, Request $request, EntityManagerInterface $entityManager, UploadService $uploadService): Response
    {
        $user = $this->getUser();
        
        $image = new Image();
        $image->setProduct($product);
        
        $form = $this->createForm(Image1Type::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile =$form->get('picture')->getData();
            
            if ($imageFile) {
                $file = $uploadService->upload($imageFile);
                $image->setPicture($file);
            }
            $entityManager->persist($image);

            $product->addImage($image);

            $entityManager->flush();

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('image/new.html.twig', [
            'image' => $image,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'image_show', methods: ['GET'])]
    public function show(Image $image): Response
    {
        $user = $this->getUser();
       
        return $this->render('image/show.html.twig', [
            'image' => $image,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Image $image, EntityManagerInterface $entityManager, UploadService $uploadService): Response
    {
        $user = $this->getUser();
        
        $form = $this->createForm(Image1Type::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $oldFile = $image->getPicture();
                $file = $uploadService->upload($imageFile, $oldFile);
                 $image->setPicture($file);

            }
            $entityManager->flush();

            return $this->redirectToRoute('image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('image/edit.html.twig', [
            'image' => $image,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager, UploadService $uploadService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $uploadService->delete($image->getPicture());
            
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('image_index', [], Response::HTTP_SEE_OTHER);
    }
}
