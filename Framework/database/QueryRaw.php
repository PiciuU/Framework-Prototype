<?php

namespace Database;

class QueryRaw
{
    private static $instance;

    const PARAM_INT = 1;
    const PARAM_STR = 2;

    protected $params = array();

    protected $query = null;

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new QueryRaw();
        }
        return self::$instance;
    }

    public function params(array ...$params) {
        $this->params = $params;
        return $this;
    }

    public function query(string $query) {
        $this->query = $query;
        return $this;
    }

    public function fetch($fetchAsObject = false) {
        $db = new DatabaseConnection();
        $db_result = $db->fetch($this->query, $this->params, $fetchAsObject);
        self::clearValues();
        return $db_result;
    }

    public function execute() {
        $db = new DatabaseConnection();
        $db_result = $db->execute($this->query, $this->params);
        self::clearValues();
        return $db_result;
    }

    public function clearValues() {
        $this->params = array();
        $this->query = null;
        $this->fields = ['*'];
        $this->conditions = [];
        $this->limit = null;
    }

    public function getLastInsertId() {
        return DatabaseConnection::getLastInsertId();
    }
}
