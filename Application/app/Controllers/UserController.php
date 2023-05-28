<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\UserAccessToken;

use Symfony\Component\HttpFoundation\Request;
use Services\Support\Response;
use Services\Support\Validator;
use Services\Support\Hash;

class UserController {

    public function register(Request $request) {
        $validator = new Validator($request);

        $validator->required(['login', 'password', 'password_confirmation'])
                ->min(['login', 3], ['password', 3], ['password_confirmation', 3])
                ->equals(['password', $validator->get('password_confirmation')]);

        if ($validator->fails()) {
            Response::failure($validator->errors());
        }

        if (User::instance()->create([
            'login' => $validator->get('login'),
            'password' => Hash::make($validator->get('password'), ['rounds' => 12])
        ]) === false) Response::failure("Nie udało się założyć konta");


        $user = new User(User::instance()->select(['id','login'])->where('login', $validator->get('login'))->first());
        $token = $user->createToken($request->getClientIp());

        $user_data = [
            'id' => $user->getId(),
            'login' => $user->getLogin()
        ];

        Response::success("Pomyślnie założono konto", ['user' => $user_data, 'token' => $token]);
    }

    public function login(Request $request) {
        $validator = new Validator($request);

        $validator->required(['login', 'password']);

        if ($validator->fails()) {
            Response::failure($validator->errors());
        }

        $user = new User(User::instance()->where('login', $validator->get('login'))->first());

        if (!$user || !Hash::check($validator->get('password'), $user->getPassword())) Response::failure("Nie udało się zalogować");

        $user = new User(User::instance()->select(['id','login'])->where('login', $validator->get('login'))->first());
        $token = $user->createToken($request->getClientIp());

        $user_data = [
            'id' => $user->getId(),
            'login' => $user->getLogin()
        ];

        Response::success("Pomyślnie zalogowano", ['user' => $user_data, 'token' => $token]);
    }

    public function user(Request $request) {
        Response::success("Pomyślnie pobrano dane użytkownika", ['id' => $request->user->getId(), 'login' => $request->user->getLogin()]);
    }

    public function logout(Request $request) {
        $request->user->deleteToken();

        Response::success("Pomyślnie wylogowano z konta");
    }
}