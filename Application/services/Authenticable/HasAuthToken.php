<?php

namespace Services\Authenticable;

use App\Models\UserAccessToken;
use App\Models\User;

use Services\Support\Response;

class HasAuthToken {

    private $token;
    private $request;

    public function __construct($request) {
        $this->request = $request;
        self::hasToken();
    }

    public function hasToken() {
       if (ROUTING_TYPE == 'api') $authorizationHeader = $this->request->headers->get('Authorization');
       else $authorizationHeader = 'Bearer '.$this->request->cookies->get('auth-token');

        if (!empty($authorizationHeader)) {
            if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $token)) {
                $this->token = $token[1];
            }
        } else {
            $this->token = null;
        }
    }

    public function isAuthorized() {
        if (!$this->token) return false;

        $userAccessToken = new UserAccessToken(UserAccessToken::instance()->where('token', $this->token)->first());

        if (!$userAccessToken->getUserId()) return false;

        $user = new User(User::instance()->where('id', $userAccessToken->getUserId())->first());

        if (!$user) return false;

        $this->request->user = $user;
        $this->request->user->token = $this->token;

        return true;
    }
}