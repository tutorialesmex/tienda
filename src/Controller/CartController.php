<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Sale;
use App\Service\CartService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart/{id}', name: 'app_cart_add')]
    public function index(int $id, Request $request): Response
    {
        $cookies = $request->cookies;
        $products_cookies = $cookies->get('cart_products') ? $cookies->get('cart_products') : '[]';
        //dump($products_cookies);die();
        $response = $this->redirectToRoute('app_product_show', ['id' => $id]);

        $cartService = new CartService();
        $cartService->addToCart($id, $products_cookies, $cookies, $response);        
        
        $this->addFlash("success", "Se agrego el producto");

        return $response;
        /*return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);*/
    }


    #[Route('/show/cart', name: 'app_cart_show')]
    public function show(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cookies = $request->cookies;
        $products_cookies = $cookies->get('cart_products') ? $cookies->get('cart_products') : '[]';
        $products_cookies = json_decode($products_cookies, true);

        $products = $entityManager->getRepository(Product::class)->findBy(['id' => array_keys($products_cookies)]);
        //$this->addFlash("success", "Tienes ".count($products)." articulos en el carrito.");
        return $this->render('cart/show.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/terminar/cart', name: 'app_cart_terminar')]
    public function terminar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cookies = $request->cookies;
        $products_cookies = $cookies->get('cart_products') ? $cookies->get('cart_products') : '[]';
        $products_cookies = json_decode($products_cookies, true);

        $products = $entityManager->getRepository(Product::class)->findBy(['id' => array_keys($products_cookies)]);

        $client = new Client();
        $client->setEmail('compra@gmail.com');
        $client->setFullName('Compra de prueba');

        $sale = new Sale();
        $sale->setUser($client);
        $sale->setDateCreated(new DateTime());
        $entityManager->persist($sale);
        $entityManager->flush();

        foreach ($products as $key => $product) {
            $cart = new Cart();
            $cart->setSale($sale);
            $cart->setProduct($product);
            $cart->setLastUpdate(new DateTime());
            $cart->setItems($products_cookies[$product->getId()]);
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        $response = new RedirectResponse('https://api.whatsapp.com/send?phone=52551111111&text=Hola, quiero comprar: ' . count($products) . ' productos');

        $cartService = new CartService();
        $cartService->cleanCart($response);

        return $response;
        /*return $this->render('cart/show.html.twig', [
            'products' => $products,
        ]);*/
    }

    #[Route('/clean/cart', name: 'app_cart_clean')]
    public function cleanCart(Request $request): Response
    {        
        $response=$this->redirectToRoute('app_product_index');
        $cartService = new CartService();
        $cartService->cleanCart($response);

        return $response;
        /*return $this->render('cart/show.html.twig', [
            'products' => $products,
        ]);*/
    }
}
