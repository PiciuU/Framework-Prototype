let currentProduct;

setTimeout(getUpdatedProducts, getNextUpdateTime());

function getUpdatedProducts() {
	fetch(websiteUrl + '/api/user/products', {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + getAuthToken()
		}
	})
		.then((response) => response.json())
		.then((response) => {
			if (response.success == false) throw response.message;
			renderTable(response.data);
			setTimeout(getUpdatedProducts, getNextUpdateTime());
		})
		.catch((error) => console.log(error));
}

function getNextUpdateTime() {
	const currentDate = new Date();
	let ms = 1000 * 60 * 5.5;
	let data = new Date(Math.ceil(currentDate / ms) * ms);
	let diff = Math.abs(currentDate - data);
	let sec = Math.floor(diff / 1000);
	if (sec == 0) sec = ms / 1000;
	return sec * 1000;
}

function addProduct() {
	const btn = document.querySelector('#add-product-btn');
	btn.disabled = true;

	fetch(websiteUrl + '/api/user/products', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + getAuthToken()
		},
		body: JSON.stringify({
			store: document.querySelector('#form-store').value,
			url: document.querySelector('#form-link').value,
			price: document.querySelector('#form-price').value
		})
	})
		.then((response) => response.json())
		.then((response) => {
			if (response.success == false) throw response.message;
			addProductToTable(response.data);
			clearAddProductForm();
		})
		.catch((error) => console.log(error))
		.finally(() => (btn.disabled = false));
}

function clearAddProductForm() {
	document.querySelector('#form-store').value = '';
	document.querySelector('#form-link').value = '';
	document.querySelector('#form-price').value = '';
}

function editProduct(id) {
	currentProduct = productsList[productsList.findIndex((product) => parseInt(product.id) === id)];
	document.querySelector('body').classList.add('disable_scroll');
	document.querySelector('#edit-product-modal').classList.remove('hide');
	document.querySelector('#modal-product-url').value = currentProduct['url'];
	document.querySelector('#modal-product-name').value = currentProduct['name'];
	document.querySelector('#modal-product-price').value = currentProduct['max_price'];
	document.querySelector('#modal-product-notification').checked = parseInt(currentProduct['is_notification_enabled']);
	document.querySelector('#modal-product-last-update').textContent = currentProduct['updated_at'];
}

function updateProduct() {
	if (!currentProduct) return;

	const btn = document.querySelector('#update-product-btn');
	btn.disabled = true;

	fetch(websiteUrl + '/api/user/products', {
		method: 'PUT',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + getAuthToken()
		},
		body: JSON.stringify({
			id: currentProduct.id,
			max_price: document.querySelector('#modal-product-price').value,
			is_notification_enabled: document.querySelector('#modal-product-notification').checked
		})
	})
		.then((response) => response.json())
		.then((response) => {
			if (response.success == false) throw response.message;
			closeModal();
			updateProductFromTable(currentProduct.id, response.data);
		})
		.catch((error) => console.log(error))
		.finally(() => (btn.disabled = false));
}

function deleteProduct(id) {
	if (!confirm('Czy na pewno chcesz usunać wybrany produkt z listy obserwowanych?')) return;

	fetch(websiteUrl + '/api/user/products', {
		method: 'DELETE',
		headers: {
			'Content-Type': 'application/json',
			Authorization: 'Bearer ' + getAuthToken()
		},
		body: JSON.stringify({
			id: id
		})
	})
		.then((response) => response.json())
		.then((response) => {
			if (response.success == false) throw response.message;
			removeProductFromTable(id);
		})
		.catch((error) => console.log(error));
}

function closeModal() {
	document.querySelector('body').classList.remove('disable_scroll');
	document.querySelector('#edit-product-modal').classList.add('hide');
}
