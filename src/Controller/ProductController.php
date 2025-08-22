<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Tags;
use App\Form\ProductForm;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tags = [];
            foreach ($product->getTags() as $key => $value) {
                $tags[] = $value->getValue();
            }
            $product->setTags($tags);

            $fileUploader=new FileUploader('fotos',$slugger);
            $fotos=[];
            $imagesRaw=$request->files->get('product_form')['images'];
            foreach ($imagesRaw as $key => $value) {
                $path= $fileUploader->upload($value);                
                $fotos[]='fotos/'.$path;
            }
            $product->setImages($fotos);
            
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $tags = [];
        foreach ($product->getTags() as $key => $value) {
            $tag = new Tags();
            $tag->setValue($value);
            $tags[] = $tag;
        }

        $product->setTags($tags);

        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tags = [];
            foreach ($product->getTags() as $key => $value) {
                $tags[] = $value->getValue();
            }
            $product->setTags($tags);

            $fileUploader=new FileUploader('fotos',$slugger);
            $fotos=[];
            $imagesRaw=$request->files->get('product_form')['images'];
            foreach ($imagesRaw as $key => $value) {
                $path= $fileUploader->upload($value);                
                $fotos[]='fotos/'.$path;
            }
            $product->setImages($fotos);

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
