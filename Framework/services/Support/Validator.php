<?php

namespace Services\Support;

use Symfony\Component\HttpFoundation\Request;

class Validator {
    private $errors = [];
    private $requests;

    public function __construct(Request $request) {
        $requests = self::createParamsFromRequest($request);
        $this->requests = $requests = array_map(fn($element) => htmlspecialchars($element), $requests);
    }

    public function get($key) {
        return $this->requests[$key] ?? null;
    }

    public function required($fields) {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $this->requests) || $this->requests[$field] === null) {
                array_push($this->errors, "Field $field is required");
            }
        }
        return $this;
    }

    public function type(array ...$fields) {
        if (self::fails()) return $this;
        foreach($fields as $field) {
            if ($field[1] === 'integer') {
                if (is_numeric($this->requests[$field[0]])) $this->requests[$field[0]] = (int)$this->requests[$field[0]];
                else array_push($this->errors, "Field $field[0] must be an integer");
            }
            else if ($field[1] === 'string' && gettype($this->requests[$field[0]]) !== 'string') {
                array_push($this->errors, "Field $field[0] must be a string");
            }
        }
        return $this;
    }

    public function min(array ...$fields) {
        if (self::fails()) return $this;
        foreach($fields as $field) {
            if (gettype($this->requests[$field[0]]) === 'string' && $field[1] > strlen($this->requests[$field[0]])) {
                array_push($this->errors, "Field $field[0] must be at least $field[1] characters long");
            } else if (is_numeric($this->requests[$field[0]]) && $field[1] > $this->requests[$field[0]]) {
                array_push($this->errors, "Field $field[0] must be greater than or equal $field[1]");
            }
        }
        return $this;
    }

    public function max(array ...$fields) {
        if (self::fails()) return $this;
        foreach($fields as $field) {
            if (gettype($this->requests[$field[0]]) === 'string' && $field[1] < strlen($this->requests[$field[0]])) {
                array_push($this->errors, "Field $field[0] cannot be longer than $field[1] characters");
            } else if (is_numeric($this->requests[$field[0]]) && $field[1] < $this->requests[$field[0]]) {
                array_push($this->errors, "Field $field[0] must be lower than or equal $field[1]");
            }
        }
        return $this;
    }

    public function pattern(array ...$fields) {
        if (self::fails()) return $this;
        foreach($fields as $field) {
            if (preg_match($field[1], $this->requests[$field[0]]) != 1) {
                array_push($this->errors, "Field $field[0] doesn't match pattern");
            }
        }
        return $this;
    }

    public function in(array ...$fields) {
        if (self::fails()) return $this;
        foreach($fields as $field) {
            if (!in_array($this->requests[$field[0]], $field[1])) array_push($this->errors, "Field $field[0] is invalid");
        }
        return $this;
    }

    public function contains(array ...$fields) {
        if (self::fails()) return $this;
        foreach($fields as $field) {
            if (!str_contains($this->requests[$field[0]], $field[1])) array_push($this->errors, "Field $field[0] must contain \"$field[1]\"");
        }
        return $this;
    }

    public function equals(array ...$fields) {
        if (self::fails()) return $this;
        foreach($fields as $field) {
            if ($this->requests[$field[0]] !== $field[1]) array_push($this->errors, "Field $field[0] is different from $field[0]_confirmation");
        }
    }

    public function fails() {
        return count($this->errors) == 0 ? false : true;
    }

    public function errors() {
        return $this->errors;
    }

    protected function createParamsFromRequest(Request $request) {
        $params = [];
        if ($request->getContentType() == 'form') {
            foreach($request->request as $key => $value) {
                $params[$key] = $value;
            }
        }
        else if ($request->getContent()) {
            foreach(json_decode($request->getContent()) as $key => $value) {
                $params[$key] = $value;
            }
        }
        return $params;
    }
}