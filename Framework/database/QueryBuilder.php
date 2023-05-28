<?php

namespace Database;

class QueryBuilder extends QueryRaw
{
    private static $instance;
    private $table = null;

    protected $fields = ['*'];
    protected $conditions = [];
    protected $limit = null;

    public function setTable($table) {
        $this->table = $table;
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new QueryBuilder();
        }
        self::$instance->table = static::$table;
        return self::$instance;
    }

    public function select(array $fields = ['*']) {
        $this->fields = $fields;
        return $this;
    }

    public function where($arg0, $arg1, $arg2 = null) {
        $condition = (object)[
            'field' => $arg0,
            'operator' => $arg2 === null ? '=' : $arg1,
            'value' => $arg2 === null ? $arg1 : $arg2
        ];
        self::createParamFromCondition($condition);
        array_push($this->conditions, "$condition->field $condition->operator :$condition->field");
        return $this;
    }

    public function limit(int $limit) {
        if ($limit > 0) $this->limit = $limit;
        return $this;
    }

    public function first() {
        $this->limit = 1;
        $result = self::get();
        return $result[0] ?? [];
    }

    public function get($fetchAsObject = false) {
        $where = $this->conditions === [] ? '' : ' WHERE ' . implode(' AND ', $this->conditions);
        $this->query = 'SELECT ' . implode(', ', $this->fields)
            . ' FROM ' . $this->table
            . $where;
        if ($this->limit) $this->query .= ' LIMIT '.$this->limit;
        return self::fetch($fetchAsObject);
    }

    public function createParamFromCondition($condition) {
        array_push($this->params,
            array($condition->field, $condition->value, is_numeric($condition->value) && !$this->containsDecimal($condition->value) ? self::PARAM_INT : self::PARAM_STR)
        );
    }

    public function containsDecimal($value) {
        if (strpos($value, ".") !== false) return true;
        return false;
    }

    public function create(array $fields = []) {
        $keys = array();
        foreach($fields as $key => $value) {
            array_push($keys, $key);
            $condition = (object)[
                'field' => $key,
                'value' => $value
            ];
            self::createParamFromCondition($condition);
        }
        $this->query = 'INSERT INTO ' . $this->table . '(' . implode(', ', $keys) . ') VALUES(:' . implode(', :', $keys) . ')';
        return self::execute();
    }

    public function update(array $fields = []) {
        if ($this->isEmpty()) return false;
        self::where('id', $this->getId());

        $keys = array();
        foreach($fields as $key => $value) {
            array_push($keys, $key);
            $condition = (object)[
                'field' => $key,
                'value' => $value
            ];
            self::createParamFromCondition($condition);
        }
        $this->query = 'UPDATE ' . $this->table . ' SET ';
        foreach($keys as $index => $key) {
            $this->query .= "$key = :$key";
            if ($index !== array_key_last($keys)) $this->query .= ", ";
        }

        $this->query .= ' WHERE ' . implode(' AND ', $this->conditions);
        $isExecuted = self::execute();

        if ($isExecuted) $this->updateProperties($fields);

        return $isExecuted;
    }

    public function delete() {
        if ($this->isEmpty()) return false;
        self::where('id', $this->getId());

        $this->query = 'DELETE FROM ' . $this->table . ' WHERE ' . implode(' AND ', $this->conditions) . ' LIMIT 1';
        $isExecuted = self::execute();

        $this->clearProperties();

        if ($isExecuted) $this->clearProperties();

        return $isExecuted;
    }
}
