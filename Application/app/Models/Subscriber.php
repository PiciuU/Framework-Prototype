<?php

namespace App\Models;

class Subscriber extends Model {
	protected static $table = 'subscribers';

    protected $id;
    protected $userId;
    protected $endpoint;
    protected $authToken;
    protected $publicKey;
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

    public function getEndpoint() {
		return $this->endpoint;
	}

	public function setEndpoint($endpoint) {
		$this->endpoint = $endpoint;
	}

	public function getAuthToken() {
		return $this->authToken;
	}

	public function setAuthToken($authToken) {
		$this->authToken = $authToken;
	}

	public function getPublicKey() {
		return $this->publicKey;
	}

	public function setPublicKey($publicKey) {
		$this->publicKey = $publicKey;
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