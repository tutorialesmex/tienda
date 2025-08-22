<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CustomTwigExtension extends AbstractExtension
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('show_images', [$this, 'showImages']),
            new TwigFunction('load_products', [$this, 'getProducts']),
        ];
    }

    public function showImages(array $json): string
    {
        $fotos = "";
        foreach ($json as $key => $value) {
            $fotos .= '<img src="/' . $value . '" width="400" />';
        }
        return $fotos;
    }

    public function getProducts($product_ids):string
    {
        $product_ids=json_decode($product_ids,true);
        $products = $this->entityManager->getRepository(Product::class)->findBy(['id' => array_keys($product_ids)]);
        
        return 'Productos('.count($products).')';
    }
}
