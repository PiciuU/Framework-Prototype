const websiteUrl = window.location.href.substring(0, window.location.href.length - 1);

function changeContainer(container) {
	if (container == 'register') {
		document.querySelector('#login-container').classList.add('hide');
		document.querySelector('#register-container').classList.remove('hide');
	} else {
		document.querySelector('#login-container').classList.remove('hide');
		document.querySelector('#register-container').classList.add('hide');
	}
}

function registerUser() {
	if (document.querySelector('#register-form-password').value != document.querySelector('#register-form-password-confirmation').value) {
		alert('Podane hasłą nie zgadzają się!');
		return;
	}
	const btn = document.querySelector('#register-btn');
	btn.disabled = true;

	fetch(websiteUrl + '/api/register', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json'
		},
		body: JSON.stringify({
			login: document.querySelector('#register-form-login').value,
			password: document.querySelector('#register-form-password').value,
			password_confirmation: document.querySelector('#register-form-password-confirmation').value
		})
	})
		.then((response) => response.json())
		.then((response) => {
			if (response.success == false) throw response.message;
			setAuthToken(response.data.token);
			location.reload();
		})
		.catch((error) => console.log(error))
		.finally(() => (btn.disabled = false));
}

function loginUser() {
	const btn = document.querySelector('#login-btn');
	btn.disabled = true;

	fetch(websiteUrl + '/api/login', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json'
		},
		body: JSON.stringify({
			login: document.querySelector('#login-form-login').value,
			password: document.querySelector('#login-form-password').value
		})
	})
		.then((response) => response.json())
		.then((response) => {
			if (response.success == false) throw response.message;
			setAuthToken(response.data.token);
			location.reload();
		})
		.catch((error) => console.log(error))
		.finally(() => (btn.disabled = false));
}

function setAuthToken(token) {
	let date = new Date();
	date.setTime(date.getTime() + 365 * 24 * 60 * 60 * 1000);
	const expires = 'expires=' + date.toUTCString();
	document.cookie = 'auth-token=' + token + '; ' + expires + '; path=/';
}
