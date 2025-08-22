<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Response;

class CartService
{

    public function addToCart($element, $products_cookies, InputBag $cookie, Response $response)
    {
        $products_cookies = json_decode($products_cookies, true);
        $all = $this->unified($element, $products_cookies);
        $cookie = Cookie::create('cart_products')
            ->withValue(json_encode($all))
            ->withExpires(strtotime('Fri, 20-May-2030 15:25:52 GMT'))
            ->withSecure(true);

        $response->headers->setCookie($cookie);
    }

    public function cleanCart(Response $response){
        $response->headers->clearCookie('cart_products');
    }
    private function unified(int $id, array $all): array
    {
        $exist = $this->exist($id, $all);

        if ($exist == true) {
            $all[$id] = $all[$id] + 1;
        } else {
            $all[$id] = 1;
        }

        return $all;
    }

    private function exist(int $id, array $all): bool
    {
        foreach ($all as $key => $value) {
            if ($id == $key) {
                return true;
            }
        }
        return false;
    }
}
