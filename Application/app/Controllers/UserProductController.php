<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\UserProduct;

use Symfony\Component\HttpFoundation\Request;
use Services\Support\Response;
use Services\Support\Validator;

use Database\QueryRaw;

class UserProductController {

    public function create(Request $request, Validator $validator, int $product_id) {
        if (UserProduct::instance()->create([
            'user_id' => $request->user->getId(),
            'product_id' => $product_id,
            'max_price' => $validator->get('price')
        ]) === false) return false;
        return true;
    }

    public function index(Request $request, $returnAsJson = true) {
        $userProducts = QueryRaw::instance()->params(
                array('user_id', $request->user->getId(), QueryRaw::PARAM_STR)
            )->query("SELECT up.id, p.name, p.url,  p.is_available, p.last_known_price, p.last_available_at, up.max_price, up.is_notification_enabled, p.updated_at
                FROM users_products up
                JOIN products p ON p.id = up.product_id
                WHERE up.user_id = :user_id")->fetch(true);

        if ($returnAsJson) {
            if (!$userProducts) Response::success("Nie znaleziono żadnych obserwowanych produktów.");
            Response::success("Znaleziono obserwowane produkty.", $userProducts);
        }
        else return $userProducts;
    }

    public function show(Request $request, int $user_product_id, $returnAsJson = true) {
        $userProducts = QueryRaw::instance()->params(
            array('user_id', $request->user->getId(), QueryRaw::PARAM_INT),
            array('user_product_id', $user_product_id, QueryRaw::PARAM_INT)
        )->query("SELECT up.id, p.name, p.url,  p.is_available, p.last_known_price, p.last_available_at, up.max_price, up.is_notification_enabled, p.updated_at
            FROM users_products up
            JOIN products p ON p.id = up.product_id
            WHERE up.user_id = :user_id AND up.id = :user_product_id LIMIT 1")->fetch(true);

        if ($returnAsJson) {
            if (!$userProducts) Response::success("Nie znaleziono żadnych obserwowanych produktów.");
            Response::success("Znaleziono obserwowane produkty.", $userProducts[0]);
        }
        else return $userProducts[0];
    }

    public function update(Request $request) {
        $validator = new Validator($request);

        $validator->required(['id', 'max_price', 'is_notification_enabled'])
                ->type(['id', 'integer'], ['max_price', 'integer', 'is_notification_enabled', 'integer']);

        if ($validator->fails()) {
            Response::failure($validator->errors());
        }

        $userProduct = new UserProduct(UserProduct::instance()->where('id', $validator->get('id'))->first());

        if (!$userProduct) Response::failure("Nie znaleziono produktu do edycji.");

        if ($userProduct->update([
            'max_price' => $validator->get('max_price'),
            'is_notification_enabled' => $validator->get('is_notification_enabled')
        ]) === false) Respone::failure("Nie udało się zaktualizować produktu.");

        Response::success("Pomyślnie zaktualizowano produkt.", self::show($request, $validator->get('id'), false));
    }

    public function delete(Request $request) {
        $validator = new Validator($request);

        $validator->required(['id'])
                ->type(['id', 'integer']);

        if ($validator->fails()) {
            Response::failure($validator->errors());
        }

        $userProduct = new UserProduct(UserProduct::instance()->where('id', $validator->get('id'))->first());

        if (!$userProduct) Response::success("Nie znaleziono produktu do usunięcia.");

        if ($userProduct->delete() === false) Response::failure("Nie udało się usunąć produktu.");

        $productUsers = QueryRaw::instance()->params(
                array('product_id', $userProduct->getProductId(), QueryRaw::PARAM_INT)
            )->query("SELECT COUNT(id) as number FROM users_products WHERE product_id = :product_id")->fetch();

        if ($productUsers[0]['number'] == 0) {
            $product = new Product(['id' => $userProduct->getProductId()]);
            $product->delete();
        }

        Response::success("Pomyślnie usunięto produkt.");

    }

}