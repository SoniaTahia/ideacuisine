<?php

namespace App\Controller;


use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Service\UploadService;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/{category}/{page}/{image}',
    name: 'product_index', 
    defaults: ['category' => 0, 'page'=> 1, 'image' => 3],  
    requirements: ["page" => "\d+", "category" => "\d+", "image" => "\d+"], 
    methods: ['GET'])]
    public function index(?Category $category, ?Image $image, int $page, ProductRepository $productRep, ImageRepository $imageRep): Response
    {   
        $user = $this->getUser();
        
        $productPerPage = 6;
        $productsCount = $productRep->count([]);
        $pages = [];
        $pageCounter = 0;
        for ($i = 0; $i < $productsCount; $i += $productPerPage) {
            $pageCounter++;
            $pages[] = $pageCounter;
        }
        if ($page > $productsCount / $productPerPage) {
            $page = $pageCounter;
        }
        if ($page < 1) {
            $page = 1;
        }
        if (!$category) {
            $productCriteria = [];
            $categoryName = "";
        } else {
            $productCriteria = [
                'category' => $category,
            ];
            $categoryName = $category->getName();
        }
        if (!$image) {
        $imageCriteria = [];
        $imagePicture = "";
        } else{
            $imageCriteria = [
                'images' => $images,
            ];
            $imagePicture = $image->getPicture();
        }
        
        $products = $productRep->findBy(
            $productCriteria,
            [
                'name' => "ASC",
            ],
            $productPerPage,
            ($page - 1) * $productPerPage,
        );
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'actualPage' => $page,
            'pages' => $pages,
            'lastPage' => $pageCounter,
            'categoryName' => $categoryName,
            'imagePicture' => $imagePicture,
            'user' => $user,
        ]);   
}

    #[Route('/list/{page}',
    name: 'product_list', 
    defaults: ['page' => 1], 
    requirements: ["page" => "\d+"],
    methods: ['GET'])]
        public function list(ProductRepository $productRep, int $page): Response
        {
        $user = $this->getUser();

        $productPerPage = 8;
        $productsCount = $productRep->count([]);
        $pages = [];
        $pageCounter = 0;
        for ($i = 0; $i < $productsCount; $i += $productPerPage) {
            $pageCounter++;
            $pages[] = $pageCounter;
        }
        if ($page > $productsCount / $productPerPage) {
            $page = $pageCounter;
        }

        if ($page < 1) {
            $page = 1;
        }
        
        $products = $productRep->findBy(
            [],
            [
                'id' => "ASC",
            ],
            $productPerPage,
            ($page - 1) * $productPerPage,
        );
        
        return $this->render('product/list.html.twig', [
            'products' => $products,
            'actualPage' => $page,
            'pages' => $pages,
            'lastPage' => $pageCounter,
            'user' => $user,
        ]);
    }


    #[Route('/new', name: 'product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }
       
        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/show', name: 'product_show', methods: ['GET'])]
    public function show(Product $product, ImageRepository $imageRep): Response
    {
        $user = $this->getUser();
        
        $imageCriteria = [
            'product' => $product,
        ];
        $images = $imageRep->findBy($imageCriteria);
        
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'images' => $images,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager,  UploadService $uploadService, ImageRepository $imageRep): Response
    {
        $user = $this->getUser();
        
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        $imageCriteria = [
            'product' => $product,
        ];
        $images = $imageRep->findBy($imageCriteria);
        
        
        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'images' => $images,
            'user'=> $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager, ImageRepository $imageRepo, UploadService $uploadService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $images = $product->getImages();
            foreach ($images as $image) {
                $uploadService->delete($image->getPicure());
                $imageRepo->delete($image);
            }
           
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }
}

