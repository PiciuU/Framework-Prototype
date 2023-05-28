<?php

namespace App\Models;

class Product extends Model {
	protected static $table = 'products';

    protected $id;
    protected $url;
    protected $name;
	protected $image;
    protected $isAvailable;
    protected $lastKnownPrice;
    protected $lastAvailableAt;
    protected $createdAt;
    protected $updatedAt;

    public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url) {
		$this->url = $url;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getImage() {
		return $this->image;
	}

	public function setImage($image) {
		$this->image = $image;
	}

	public function getIsAvailable() {
		return $this->isAvailable;
	}

	public function setIsAvailable($isAvailable) {
		$this->isAvailable = $isAvailable;
	}

	public function getLastKnownPrice() {
		return $this->lastKnownPrice;
	}

	public function setLastKnownPrice($lastKnownPrice) {
		$this->lastKnownPrice = $lastKnownPrice;
	}

	public function getLastAvailableAt() {
		return $this->lastAvailableAt;
	}

	public function setLastAvailableAt($lastAvailableAt) {
		$this->lastAvailableAt = $lastAvailableAt;
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