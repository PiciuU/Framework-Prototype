<?php

namespace App\Controllers;

use App\Controllers\UserProductController;
use App\Controllers\ScrapController;

use App\Models\Product;
use App\Models\UserProduct;

use Symfony\Component\HttpFoundation\Request;
use Services\Support\Response;
use Services\Support\Validator;

class ProductController {

    public function store(Request $request) {
        $validator = new Validator($request);

        $store_list = ['morele.net', 'proshop.pl'];

        $validator->required(['store', 'url', 'price'])
                ->type(['price', 'integer'])
                ->in(['store', $store_list])
                ->contains(['url', $validator->get('store')]);

        if ($validator->fails()) {
            Response::failure($validator->errors());
        }

        $product = new Product(Product::instance()->where('url', $validator->get('url'))->first());

        $product_id = $product->getId();

        // Create product if not exists

        if ($product->isEmpty()) {
            if (Product::instance()->create([
                'url' => $validator->get('url')
            ]) === false) Response::failure("Nie udało się dodać nowego produktu.");
            $product_id = Product::instance()->getLastInsertId();

            $scraper = new ScrapController();
            $scraper->scrapOne($request, $product_id, false);
        }

        // Add product observation

        $controller = new UserProductController();

        if (!$controller->create($request, $validator, $product_id)) Response::failure("Nie udało się dodać obserwacji wprowadzonego produktu, upewnij się czy nie obserwujesz już tego produktu.");

        $user_product_id = UserProduct::instance()->getLastInsertId();

        Response::success('Pomyślnie rozpoczęto obserwację produktu.', $controller->show($request, $user_product_id, false));
    }
}