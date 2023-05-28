const websiteUrl = window.location.href.substring(0, window.location.href.length - 1);

function logout() {
	fetch(websiteUrl + '/api/logout', {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + getAuthToken()
		}
	}).finally(() => {
		deleteAuthToken();
		location.reload();
	});
}

function getAuthToken() {
	const name = 'auth-token=';
	const cDecoded = decodeURIComponent(document.cookie);
	const cArr = cDecoded.split('; ');
	let res;
	cArr.forEach((val) => {
		if (val.indexOf(name) === 0) res = val.substring(name.length);
	});

	return res ? res : false;
}

function deleteAuthToken() {
	document.cookie = 'auth-token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
}
