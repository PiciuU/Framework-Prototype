<?php

namespace Services\Authenticable;

use App\Models\UserAccessToken;

trait Tokenable {

    public $token;

    public function createToken($ip) {
        $token = bin2hex(random_bytes(32));

        UserAccessToken::instance()->create([
            'user_id' => $this->getId(),
            'token' => $token,
            'ip_address' => $ip
        ]);

        $this->token = $token;
        return $token;
    }

    public function deleteToken() {
        $userToken = new UserAccessToken(UserAccessToken::instance()->where('token', $this->token)->first());

        if (!$userToken->delete()) return false;
        return true;
    }
}