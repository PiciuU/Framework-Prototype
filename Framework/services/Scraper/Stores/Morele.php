<?php

namespace Services\Scraper\Stores;

use Services\Scraper\Curl;

class Morele extends Curl {
    private $defaultUrl = 'https://www.morele.net';
    private $basketUrl = 'https://www.morele.net/koszyk/';
    private $product = null;

    public function injectCookies() {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $this->CURL_RESULT, $matches);
        $sessid = null;
        $cart = null;
        foreach($matches[1] as $item) {
            if (strpos($item, "PHPSESSID") !== false) {
                parse_str($item, $cookie);
                $sessid = $cookie["PHPSESSID"];
            } else if (strpos($item, "cart") !== false) {
                parse_str($item, $cookie);
                $cart = $cookie["cart"];
            }
        }
        if ($sessid && $cart) $this->COOKIES = "PHPSESSID=".$sessid.";cart=".$cart;
    }

    public function fetchProduct() {
        $element = $this->getXPath()->query("//h1[@class='prod-name']")->item(0);
        if (!$element) return false;

        $name = 'Morele.net | '.$element->getAttribute('data-default');

        $element = $this->getXPath()->query("//div[@class='delivery-message-widget-for-product-params']")->item(0);
        if (!$element) return false;

        $price = $element->getAttribute('data-productprice');

        $element = $this->getXPath()->query("//img[@itemprop='image']")->item(0);
        if ($element) $image = $element->getAttribute('data-src');
        else $image = null;

        $this->product = [
            'name' => $name,
            'price' => $price,
            'image' => $image
        ];

        return true;
    }

    public function isProductAvailable() {
        $productId = $this->getXPath()->query("//div[@class='delivery-message-widget-for-product-params']")->item(0)->getAttribute('data-product-id');
        $productAvailability = $this->getXPath()->query("//link[@itemprop='availability']")->item(0)->getAttribute('href');

        if ($productAvailability != 'http://schema.org/InStock') return false;

        $this->fetchWebsite($this->defaultUrl.'/basket/add/'.$productId);

        $this->fetchWebsite($this->basketUrl);

        $isBasketEmpty = $this->getXPath()->query("//div[@class='basket-empty-container']");

        if ($isBasketEmpty->length == 1) return false;

        $productInBasket = $this->getXPath()->query("//div[contains(@class, 'js-basket-number-product-".$productId."')]")->item(0);

        if (!$productInBasket) return false;

        return true;
    }

    public function getProductName() {
        if (!$this->product) return 0;
        return $this->product['name'];
    }

    public function getProductPrice() {
        if (!$this->product) return 0;
        return $this->product['price'];
    }

    public function getProductImage() {
        if (!$this->product) return 0;
        return $this->product['image'];
    }

}