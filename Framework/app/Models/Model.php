<?php

namespace App\Models;

use Database\QueryBuilder;

class Model extends QueryBuilder {
    public function __construct(array $properties = array()) {
		self::updateProperties($properties);
		self::setTable(static::$table);
    }

	public function updateProperties(array $properties = array()) {
		foreach($properties as $key => $value) {
			$key = self::convertUnderscoreToCamel($key);
			$this->$key = $value;
		}
	}

	public function convertUnderscoreToCamel($key) {
		$key = str_replace('_', '', ucwords($key, '_'));
		$key = lcfirst($key);
		return $key;
	}

	public function clearProperties() {
		$reflect = new \ReflectionClass(get_class($this));
		$props = $reflect->getProperties();
		$ownProps = [];
		foreach ($props as $prop) {
			if ($prop->class === get_class($this) && !$prop->isStatic()) {
				$classProp = $prop->getName();
				$this->$classProp = null;
			}
		}
	}

	public function isEmpty() {
		return is_null($this->getId()) || !boolval($this->getId());
	}
}