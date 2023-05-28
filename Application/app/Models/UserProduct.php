<?php

namespace App\Models;

class UserProduct extends Model {
	protected static $table = 'users_products';

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

    public function getProductId() {
		return $this->productId;
	}

	public function setProductId($productId) {
		$this->productId = $productId;
	}

	public function getMaxPrice() {
		return $this->maxPrice;
	}

	public function setMaxPrice($maxPrice) {
		$this->maxPrice = $maxPrice;
	}

	public function getIsNotificationEnabled() {
		return $this->isNotificationEnabled;
	}

	public function setIsNotificationEnabled($isNotificationEnabled) {
		$this->isNotificationEnabled = $isNotificationEnabled;
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