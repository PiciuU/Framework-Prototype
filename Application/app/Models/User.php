<?php

namespace App\Models;

use Services\Authenticable\Tokenable;

class User extends Model {

	use Tokenable;

    protected static $table = 'users';

    protected $id;
	protected $password;
    protected $login;
    protected $createdAt;
    protected $updatedAt;

    public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getPassword() {
		return $this->password;
	}

    public function getLogin() {
		return $this->login;
	}

	public function setLogin($login) {
		$this->login = $login;
	}

    public function getCreatedAt() {
		return $this->createdAt;
	}

	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}

	public function getUpdatedAt() {
		return $this->updatedAt;
	}

	public function setUpdatedAt($updatedAt) {
		$this->updatedAt = $updatedAt;
	}

}
