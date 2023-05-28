<?php

namespace App\Controllers;

use App\Models\Product;

use App\Controllers\NotificationController;

use Symfony\Component\HttpFoundation\Request;
use Services\Support\Response;

use Services\Scraper\Stores\Morele;
use Services\Scraper\Stores\Proshop;

class ScrapController {

	public function scrapAll(Request $request) {
        $products = Product::instance()->select(['id'])->get(true);
        foreach($products as $product) {
            $this->scrapOne($request, $product->id);
        }
	}

    public function scrapOne(Request $request, int $product_id, bool $notifySubscriber = true) {
        $product = new Product(Product::instance()->where('id', $product_id)->first());
        if (!$product) return false;

        if (str_contains($product->getUrl(), 'morele.net')) {
            $scraper = new Morele($product->getUrl());
        } else if (str_contains($product->getUrl(), 'proshop.pl')) {
            $scraper = new Proshop($product->getUrl());
        } else {
            return false;
        }

        if (!$scraper->fetchProduct()) return false;

        if (!$scraper->isProductAvailable()) {
            $product->update([
                'name' => $scraper->getProductName(),
                'image' => $scraper->getProductImage(),
                'last_known_price' => $scraper->getProductPrice(),
                'is_available' => 0,
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            return true;
        }

        if ($notifySubscriber === true && ($product->getIsAvailable() === 0 || $product->getLastKnownPrice() > $scraper->getProductPrice())) {
            $product->setLastKnownPrice($scraper->getProductPrice());
            $notification = new NotificationController();
            $notification->send($product);
        }

        $product->update([
            'name' => $scraper->getProductName(),
            'image' => $scraper->getProductImage(),
            'is_available' => 1,
            'last_known_price' => $scraper->getProductPrice(),
            'last_available_at' => date("Y-m-d H:i:s")
        ]);
    }

}