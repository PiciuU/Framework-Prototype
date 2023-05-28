<?php

namespace Services\Scraper\Stores;

use Services\Scraper\Curl;

class Proshop extends Curl {
    private $defaultUrl = 'https://www.proshop.pl';
    private $basketUrl = 'https://www.proshop.pl/Basket/';
    private $product = null;

    public function injectCookies() {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $this->CURL_RESULT, $matches);
        $sessid = null;
        $cart = null;
        foreach($matches[1] as $item) {
            if (strpos($item, "ASP.NET_SessionId") !== false) {
                parse_str($item, $cookie);
                $sessid = $cookie["ASP_NET_SessionId"];
            }
        }
        if ($sessid) $this->COOKIES = "ASP.NET_SessionId=".$sessid;
    }

    public function fetchProduct() {
        $element = $this->getXPath()->query("//h1[@data-type='product']")->item(0);
        if (!$element) return false;

        $name = 'Proshop.pl | '.$element->textContent;

        $element = $this->getXPath()->query("//div[contains(@class, 'site-currency-attention')]")->item(0);
        if (!$element) $element = $this->getXPath()->query("//span[contains(@class, 'site-currency-attention')]")->item(0);
        if ($element) {
            $price = $element->textContent;
            $price = htmlentities($price);
            $price = str_replace(',', '.', $price);
            $price = str_replace('zł', '', $price);
            $price = preg_replace("/\s|&nbsp;/", '', $price);
        } else $price = null;

        $element = $this->getXPath()->query("//img[@class='h-auto']")->item(0);
        if ($element) $image = $this->defaultUrl.$element->getAttribute('src');
        else $image = null;

        $this->product = [
            'name' => $name,
            'price' => $price,
            'image' => $image
        ];

        return true;
    }

    public function isProductAvailable() {
        $isAvailable = $this->getXPath()->query("//button[@class='site-btn-addToBasket-lg']")->item(0);

        if (!$isAvailable) return false;

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
