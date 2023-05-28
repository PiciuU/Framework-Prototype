<?php

namespace App\Controllers;

use App\Models\Notification;
use App\Models\Product;

use Services\Scraper\Stores\Morele;
use Services\Scraper\Stores\Proshop;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

use Database\QueryRaw;

class NotificationController {

	public function send(Product $product) {
        $subscribers = QueryRaw::instance()->params(
            array('product_id', $product->getId(), QueryRaw::PARAM_INT),
            array('last_known_price', $product->getLastKnownPrice(), QueryRaw::PARAM_INT)
        )->query("SELECT s.* FROM users_products up
            JOIN products p ON p.id = up.product_id
            JOIN subscribers s ON s.user_id = up.user_id
            WHERE up.product_id = :product_id AND up.is_notification_enabled = 1 AND :last_known_price <= up.max_price")->fetch();

        if (!$subscribers) return false;

        $payload = array(
            'title' => "Obserwowany produkt jest dostępny",
            'content' => "Produkt ".$product->getName()." jest dostępny w cenie ".$product->getLastKnownPrice(). " zł",
            'image' => $product->getImage(),
            'url' => $product->getUrl(),
            'actions' => array(
                array(
                    'name' => 'seeMore',
                    'title' => 'Przejdź do strony'
                )
            )
        );

        $auth = array(
            'VAPID' => array(
                'subject' => config('APP_URL'),
                'publicKey' => config('VAPID_PUBLIC_KEY'),
                'privateKey' => config('VAPID_PRIVATE_KEY')
            )
        );

        $notification_successes = 0;
        $notification_expired = 0;
        $notification_failed = 0;

        $webPush = new WebPush($auth);
        $webPush->setAutomaticPadding(false);

        foreach($subscribers as $subscriber) {
            $subscription = Subscription::create([
                'endpoint' => $subscriber['endpoint'],
                'publicKey' => $subscriber['public_key'],
                'authToken' => $subscriber['auth_token']
            ]);

            $webPush->queueNotification(
                $subscription,
                json_encode($payload),
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                $notification_successes += 1;
            }
            else if ($report->isSubscriptionExpired()) {
                $this->deleteExpiredSubscription($endpoint);
                $notification_expired += 1;
            }
            else {
                $notification_failed += 1;
            }
        }

        Notification::instance()->create([
            'product_id' => $product->getId(),
            'title' => $payload['title'],
            'content' => $payload['content'],
            'attachment' => $payload['image'],
            'redirect_url' => $payload['url'],
            'notifications_sent' => $notification_successes,
            'notifications_expired' => $notification_expired,
            'notifications_failed' => $notification_failed
        ]);

	}

    public function deleteExpiredSubscription($endpoint) {
        $subscription = QueryRaw::instance()->params(
            array('endpoint', $endpoint, QueryRaw::PARAM_STR)
        )->query("DELETE FROM subscribers WHERE endpoint = :endpoint LIMIT 1")->execute();

        if ($subscription) return true;
        return false;
    }

}