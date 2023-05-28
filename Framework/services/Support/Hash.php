<?php

namespace Services\Support;

class Hash {
    public static function make($field, $options = []) {
        return password_hash($field, PASSWORD_BCRYPT, $options);
    }

    public static function check($field, $hash) {
        if (!$hash) return false;
        if (password_verify($field, $hash)) return true;
        else return false;
    }
}