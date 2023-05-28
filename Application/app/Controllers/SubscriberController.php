<?php

namespace App\Controllers;

use App\Models\Subscriber;

use Symfony\Component\HttpFoundation\Request;
use Services\Support\Response;
use Services\Support\Validator;

class SubscriberController {

    public function create(Request $request) {
        $validator = new Validator($request);

        $validator->required(['endpoint', 'auth_token', 'public_key']);

        if ($validator->fails()) {
            Response::failure($validator->errors());
        }

        $subscriber = Subscriber::instance()->create([
            'user_id' => $request->user->getId(),
            'endpoint' => $validator->get('endpoint'),
            'auth_token' => $validator->get('auth_token'),
            'public_key' => $validator->get('public_key')
        ]);

        if (!$subscriber) Response::failure("Nie udało się utworzyć subskrypcji.");
        Response::success("Subskrypcja została aktywowana.", ['token' => $request->user->token]);
    }

    public function update(Request $request) {
        $validator = new Validator($request);

        $validator->required(['endpoint', 'auth_token', 'public_key']);

        if ($validator->fails()) {
            Response::failure($validator->errors());
        }

        $subscriber = new Subscriber(Subscriber::instance()->where('endpoint', $validator->get('endpoint'))->first());

        if (!$subscriber) Response::failure("Subskrypcja nie została odnaleziona.");

        $status = $subscriber->update([
            'user_id' => $request->user->getId(),
            'endpoint' => $validator->get('endpoint'),
            'auth_token' => $validator->get('auth_token'),
            'public_key' => $validator->get('public_key')
        ]);

        if (!$status) Response::failure("Nie udało się zaktualizować subskrypcji.");
        Response::success("Subskrypcja została zaktualizowana.", ['token' => $request->user->token]);
    }

    public function delete(Request $request) {
        $validator = new Validator($request);

        $validator->required(['endpoint']);

        if ($validator->fails()) {
            Response::failure($validator->errors());
        }

        $subscriber = new Subscriber(Subscriber::instance()->where('endpoint', $validator->get('endpoint'))->first());

        if (!$subscriber) Response::failure("Subskrypcja nie została odnaleziona.");

        $status = $subscriber->delete();

        if(!$status) Response::failure("Nie udało się usunąć subskrypcji.");
        Response::success("Subskrypcja została usunięta.", ['token' => $request->user->token]);
    }
}