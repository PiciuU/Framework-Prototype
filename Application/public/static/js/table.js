let productsList = null;

function renderTable(products = null) {
	let tableBody = document.querySelector('#table-products-tbody');
	tableBody.replaceChildren();

	if (products) productsList = products;

	productsList.forEach((product) => {
		let tr = document.createElement('tr');
		tr.setAttribute('id', 'table-product-' + parseInt(product.id));
		tableBody.appendChild(tr);

		let tdProductName = document.createElement('td');
		tdProductName.innerHTML = `<a href="${product.url}" target="_blank" rel="noopener noreferrer">${product.name}</a>`;

		tr.appendChild(tdProductName);

		let tdProductAvailable = document.createElement('td');
		tdProductAvailable.classList.add('text-center');
		if (parseInt(product.is_available)) tdProductAvailable.innerHTML = `<div class="table__td-active">Tak</div>`;
		else tdProductAvailable.innerHTML = `<div class="table__td-inactive">Nie</div>`;

		if (product.last_available_at) tdProductAvailable.innerHTML = tdProductAvailable.innerHTML + `(${product.last_available_at})`;
		tr.appendChild(tdProductAvailable);

		let tdProductPrice = document.createElement('td');
		tdProductPrice.classList.add('text-center');

		tdProductPrice.innerHTML = product.last_known_price ? product.last_known_price + ' zł' : 'brak danych';
		tr.appendChild(tdProductPrice);

		let tdProductNotification = document.createElement('td');
		tdProductNotification.classList.add('text-center');

		if (parseInt(product.is_notification_enabled)) tdProductNotification.innerHTML = `<div class="table__td-active">aktywne</div>`;
		else tdProductNotification.innerHTML = `<div class="table__td-inactive">wstrzymane</div>`;

		tr.appendChild(tdProductNotification);

		let tdProductOperation = document.createElement('td');
		tdProductOperation.classList.add('text-center');

		tdProductOperation.innerHTML = `<i onclick='editProduct(${product.id})' class="fas fa-edit"></i> <i onclick="deleteProduct(${product.id})" class="fas fa-trash-alt"></i>`;

		tr.appendChild(tdProductOperation);
	});
}

function addProductToTable(product) {
	if (productsList === null || productsList.length === 0) {
		document.querySelector('#table-products-empty').classList.add('hide');
		document.querySelector('#table-products').classList.remove('hide');
	}
	productsList.push(product);
	renderTable();
}

function updateProductFromTable(id, product) {
	let index = productsList.findIndex((product) => product.id === id);
	productsList[index] = product;
	renderTable();
}

function removeProductFromTable(id) {
	let index = productsList.findIndex((product) => product.id === id);
	productsList.splice(index, 1);
	if (productsList === null || productsList.length === 0) {
		document.querySelector('#table-products-empty').classList.remove('hide');
		document.querySelector('#table-products').classList.add('hide');
	} else renderTable();
}
