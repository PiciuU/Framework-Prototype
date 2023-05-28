<?php

namespace App\Models;

class UserAccessToken extends Model {
	protected static $table = 'users_access_tokens';

    protected $id;
    protected $userId;
    protected $token;
    protected $ipAddress;
    protected $lastUsedAt;
    protected $createdAt;
    protected $updatedAt;

    public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function setUserId($userId) {
		$this->userId = $userId;
	}

	public function getToken() {
		return $this->token;
	}

	public function setToken($token) {
		$this->token = $token;
	}

	public function getIpAddress() {
		return $this->ipAddress;
	}

	public function setIpAddress($ipAddress) {
		$this->ipAddress = $ipAddress;
	}

	public function getLastUsedAt() {
		return $this->lastUsedAt;
	}

	public function setLastUsedAt($lastUsedAt) {
		$this->lastUsedAt = $lastUsedAt;
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
