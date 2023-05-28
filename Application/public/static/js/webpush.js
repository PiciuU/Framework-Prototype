'use strict';

const applicationServerPublicKey = 'BA_IHKIkAT1DaBo_oh3ScIqDTv9dkoyVSWPo6MLQcdJiqFtJ3NTum7fp_v9LoaBXAyO3xC_E5m3RejEVV7iGqOU';
let pushManager = null;
let isSubscribed = false;
let swRegistration = null;

if ('serviceWorker' in navigator) {
	window.addEventListener('load', function() {
		navigator.serviceWorker
			.register(websiteUrl + '/service-worker.js')
			.then(function(reg) {
				swRegistration = reg;

				if (!navigator.serviceWorker.controller) return;

				if ('PushManager' in window && 'showNotification' in ServiceWorkerRegistration.prototype) {
					pushManager = new PushManager(swRegistration);
					pushManager.checkSubscriptionStatus();
				}
			})
			.catch((err) => console.log('Service worker nie zainstalowany, sprawdź błąd:', err));
	});
}

class PushManager {
	constructor(serviceWorker) {
		this.serviceWorker = serviceWorker;
	}
	checkSubscriptionStatus() {
		this.serviceWorker.pushManager.getSubscription().then((subscription) => (subscription ? this.push_manageSubscription() : displayNotificationInactive())).catch((err) => {
			console.log('Krytyczny błąd PushManager: ', err);
		});
	}
	push_manageSubscription(ignoreCookies = false) {
		this.serviceWorker.pushManager
			.getSubscription()
			.then((subscription) => {
				if (!subscription) return false;
				if (convertStringToBoolean(getCookie('sw-notification-update-timeout')) && !ignoreCookies) {
					//console.log('Pomijanie aktualizacji subskrypcji', subscription);
					return subscription;
				}
				//console.log('Odświeżanie subskrybcji');
				setCookie('sw-notification-update-timeout', true, 'hours', 1);
				return this.push_sendSubscriptionToServer(subscription, 'PUT');
			})
			.then((subscription) => {
				if (!subscription) return this.push_subscribe();
				return true;
			})
			.then((subscription) => {
				if (subscription) {
					displayNotificationActive();
					// console.log('Subskrybcja jest aktywna');
					isSubscribed = true;
				} else {
					this.push_unsubscribe();
					console.log('Subskrybcja nie została aktywowana');
				}
			})
			.catch((err) => {
				console.log('Krytyczny błąd: ', err);
			});
	}
	push_subscribe() {
		const applicationServerKey = urlBase64ToUint8Array(applicationServerPublicKey);

		return this.checkNotificationPermission()
			.then(() =>
				this.serviceWorker.pushManager.subscribe({
					userVisibleOnly: true,
					applicationServerKey: applicationServerKey
				})
			)
			.then((subscription) => {
				return this.push_sendSubscriptionToServer(subscription, 'POST');
			})
			.then((subscription) => {
				if (subscription) {
					displayNotificationActive();
					return subscription;
				} else return false;
			})
			.catch((e) => {
				if (Notification.permission === 'denied') {
					console.warn('Powiadomienia zostały zablokowane.');
				} else {
					console.error('Włączenie powiadomień jest niemożliwe: ', e);
				}
			});
	}
	push_unsubscribe() {
		this.serviceWorker.pushManager
			.getSubscription()
			.then((subscription) => {
				if (!subscription) {
					return;
				}
				deleteCookies();
				displayNotificationInactive();
				return this.push_sendSubscriptionToServer(subscription, 'DELETE');
			})
			.then((subscription) => subscription.unsubscribe())
			.then(() => (isSubscribed = false))
			.catch((e) => {
				console.error('Krytyczny błąd: ', e);
			});
	}
	push_sendSubscriptionToServer(subscription, method) {
		const key = subscription.getKey('p256dh');
		const token = subscription.getKey('auth');
		return fetch(websiteUrl + '/api/subscription', {
			method,
			headers: {
				'Content-Type': 'application/json',
				Authorization: 'Bearer ' + getAuthToken()
			},
			body: JSON.stringify({
				endpoint: subscription.endpoint,
				public_key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
				auth_token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
			})
		})
			.then((response) => response.json())
			.then((response) => {
				if (response.success == false || response.data.token != getAuthToken()) {
					Promise.reject('Aktualizacja subskrybcji zakończona niepowodzeniem. Rozpoczęto próbę utworzenia nowej subskrybcji.');
					subscription.unsubscribe();
					return false;
				} else return subscription;
			})
			.catch((e) => {
				console.log('Krytyczny błąd: ', e);
			});
	}
	checkNotificationPermission() {
		return new Promise((resolve, reject) => {
			if (Notification.permission === 'denied') {
				alert('Powiadomienia w przeglądarce są zablokowane.');
				return reject(new Error('Powiadomienia są zablokowane.'));
			}

			if (Notification.permission === 'granted') {
				return resolve();
			}

			if (Notification.permission === 'default') {
				return Notification.requestPermission().then((result) => {
					if (result !== 'granted') {
						reject(new Error('Brak odpowiednich uprawnień.'));
					} else {
						resolve();
					}
				});
			}

			return reject(new Error('Nieznane uprawnienie.'));
		});
	}
}

/* Notification UI */

function enableNotifications() {
	pushManager.push_subscribe();
}

function disableNotifications() {
	pushManager.push_unsubscribe();
}

function displayNotificationInactive() {
	let section = document.querySelector('#header-notifications');
	let status = document.querySelector('#header-notifications-status');
	let button = document.querySelector('#header-enable-notification');
	let opposite_button = document.querySelector('#header-disable-notification');
	section.classList.remove('hide');
	if (status.classList.contains('notifications__active')) status.classList.remove('notifications__active');
	status.classList.add('notifications__inactive');
	status.textContent = 'wyłączone';
	if (!opposite_button.classList.contains('hide')) opposite_button.classList.add('hide');
	button.classList.remove('hide');
}

function displayNotificationActive() {
	let section = document.querySelector('#header-notifications');
	let status = document.querySelector('#header-notifications-status');
	let button = document.querySelector('#header-disable-notification');
	let opposite_button = document.querySelector('#header-enable-notification');
	section.classList.remove('hide');
	if (status.classList.contains('notifications__inactive')) status.classList.remove('notifications__inactive');
	status.classList.add('notifications__active');
	status.textContent = 'włączone';
	if (!opposite_button.classList.contains('hide')) opposite_button.classList.add('hide');
	button.classList.remove('hide');
}

/* Utility */

function urlBase64ToUint8Array(base64String) {
	const padding = '='.repeat((4 - base64String.length % 4) % 4);
	const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

	const rawData = window.atob(base64);
	const outputArray = new Uint8Array(rawData.length);

	for (let i = 0; i < rawData.length; ++i) {
		outputArray[i] = rawData.charCodeAt(i);
	}
	return outputArray;
}

function isEmpty(value) {
	if (value === null || value === 'undefined') return true;
	return false;
}

function convertStringToBoolean(value) {
	if (value === true || value === 'true') return true;
	else if (value === false || value === 'false') return false;
	return null;
}

function getCookie(cname) {
	var name = cname + '=';
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');

	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return null;
}

function setCookie(cname, cvalue, method = 'days', exvalue = 7) {
	var d = new Date();
	if (method == 'days') d.setTime(d.getTime() + exvalue * 24 * 60 * 60 * 1000);
	else d.setTime(d.getTime() + exvalue * 60 * 60 * 1000);
	var expires = 'expires=' + d.toUTCString();
	document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
}

function deleteCookies() {
	setCookie('sw-notification-update-timeout', true, 'days', -1);
}
