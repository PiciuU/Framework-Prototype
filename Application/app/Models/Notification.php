<?php

namespace App\Models;

class Notification extends Model {
	protected static $table = 'notifications';

    protected $id;
    protected $productId;
    protected $date;
    protected $title;
    protected $content;
    protected $attachment;
    protected $redirectUrl;
    protected $notificationsSent;
    protected $notificationsExpired;
    protected $notificationsFailed;

    public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}


	public function getProductId() {
		return $this->productId;
	}

	public function setProductId($productId) {
		$this->productId = $productId;
	}

	public function getDate() {
		return $this->date;
	}

	public function setDate($date) {
		$this->date = $date;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent($content) {
		$this->content = $content;
	}

	public function getAttachment() {
		return $this->attachment;
	}

	public function setAttachment($attachment) {
		$this->attachment = $attachment;
	}

	public function getRedirectUrl() {
		return $this->redirectUrl;
	}

	public function setRedirectUrl($redirectUrl) {
		$this->redirectUrl = $redirectUrl;
	}

	public function getNotificationsSent() {
		return $this->notificationsSent;
	}

	public function setNotificationsSent($notificationsSent) {
		$this->notificationsSent = $notificationsSent;
	}

	public function getNotificationsExpired() {
		return $this->notificationsExpired;
	}

	public function setNotificationsExpired($notificationsExpired) {
		$this->notificationsExpired = $notificationsExpired;
	}

	public function getNotificationsFailed() {
		return $this->notificationsFailed;
	}

	public function setNotificationsFailed($notificationsFailed) {
		$this->notificationsFailed = $notificationsFailed;
	}

}