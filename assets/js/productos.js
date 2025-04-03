function loadProducts() {
    fetch('api.php?action=get_products')
        .then(response => response.json())
        .then(data => {
            const table = document.getElementById('productsTable');
            const tbody = table.getElementsByTagName('tbody')[0];
            tbody.innerHTML = '';
            data.forEach(product => {
                const row = `<tr>
                    <td>${product.id}</td>
                    <td>${product.nombre}</td>
                    <td>${product.precio}</td>
                    <td>${product.stock}</td>
                    <td>${product.estatus}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editProduct(${product.id})">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Eliminar</button>
                    </td>
                </tr>`;
                tbody.innerHTML += row;
            });
        })
        .catch(error => console.error('Error loading products:', error));
}

function addProduct() {
    const nombre = document.getElementById('addNombre').value;
    const precio = document.getElementById('addPrecio').value;
    const stock = document.getElementById('addStock').value;
    if (!validateQuantity(stock, 'Stock') || !nombre || precio <= 0) {
        return;
    }
    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_product&nombre=${encodeURIComponent(nombre)}&precio=${precio}&stock=${stock}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Éxito', 'Producto agregado', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
            loadProducts();
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    });
}

function editProduct(id) {
    fetch('api.php?action=get_products')
        .then(response => response.json())
        .then(products => {
            const product = products.find(p => p.id == id);
            document.getElementById('editId').value = product.id;
            document.getElementById('editNombre').value = product.nombre;
            document.getElementById('editPrecio').value = product.precio;
            document.getElementById('editStock').value = product.stock;
            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        });
}

function updateProduct() {
    const id = document.getElementById('editId').value;
    const nombre = document.getElementById('editNombre').value;
    const precio = document.getElementById('editPrecio').value;
    const stock = document.getElementById('editStock').value;
    if (!validateQuantity(stock, 'Stock') || !nombre || precio <= 0) {
        return;
    }
    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=edit_product&id=${id}&nombre=${encodeURIComponent(nombre)}&precio=${precio}&stock=${stock}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Éxito', 'Producto actualizado', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
            loadProducts();
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    });
}

function deleteProduct(id) {
    Swal.fire({
        title: '¿Seguro?',
        text: 'Esto no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete_product&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Eliminado', 'Producto borrado', 'success');
                    loadProducts();
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            });
        }
    });
}