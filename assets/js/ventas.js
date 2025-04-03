let saleItems = [];

function loadProductsForSale() {
    fetch('api.php?action=get_products')
        .then(response => response.json())
        .then(data => {
            saleItems = data.filter(p => p.estatus === 'disponible');
            renderSaleItems();
        });
}

function renderSaleItems() {
    const tbody = document.getElementById('saleItemsTable');
    tbody.innerHTML = '';
    saleItems.forEach((_, index) => addSaleItemRow(index));
}

function addSaleItemRow(index = saleItems.length) {
    const tbody = document.getElementById('saleItemsTable');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select class="form-select" onchange="calculateSaleTotal()">
                ${saleItems.map(p => `<option value="${p.id}">${p.nombre} - ${p.precio}</option>`).join('')}
            </select>
        </td>
        <td>
            <div class="input-group">
                <button class="btn btn-outline-secondary" type="button" onclick="decrementValue('quantity_${index}', 1, validateQuantity, calculateSaleTotal)">-1</button>
                <input type="number" class="form-control" id="quantity_${index}" step="1" min="1" value="1" readonly>
                <button class="btn btn-outline-secondary" type="button" onclick="incrementValue('quantity_${index}', 1, validateQuantity, calculateSaleTotal)">+1</button>
            </div>
        </td>
        <td><span>0.00</span></td>
        <td><button class="btn btn-sm btn-danger" onclick="removeSaleItem(this)">Eliminar</button></td>
    `;
    tbody.appendChild(row);
}

function removeSaleItem(button) {
    button.parentElement.parentElement.remove();
    calculateSaleTotal();
}

function calculateSaleTotal() {
    let total = 0;
    const rows = document.getElementById('saleItemsTable').rows;
    for (let row of rows) {
        const select = row.cells[0].querySelector('select');
        const input = row.cells[1].querySelector('input');
        const span = row.cells[2].querySelector('span');
        if (select && input && span) {
            const product = saleItems.find(p => p.id == select.value);
            const quantity = parseInt(input.value) || 0;
            const subtotal = product.precio * quantity;
            span.textContent = subtotal.toFixed(2);
            total += subtotal;
        }
    }
    document.getElementById('saleTotal').textContent = total.toFixed(2);
}

function recordSale() {
    const rows = document.getElementById('saleItemsTable').rows;
    const items = [];
    for (let row of rows) {
        const select = row.cells[0].querySelector('select');
        const input = row.cells[1].querySelector('input');
        const quantity = parseInt(input.value) || 0;
        if (select && input && !validateQuantity(quantity, 'Cantidad')) {
            return;
        }
        if (quantity > 0) {
            items.push({ producto_id: select.value, cantidad: quantity });
        }
    }
    if (!items.length) {
        Swal.fire('Error', 'Agrega al menos un producto', 'error');
        return;
    }
    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=record_sale&items=${encodeURIComponent(JSON.stringify(items))}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Éxito', 'Venta registrada', 'success');
            bootstrap.Modal.getInstance(document.getElementById('recordSaleModal')).hide();
            loadProducts();
            loadSales();
            document.getElementById('saleItemsTable').innerHTML = '';
            document.getElementById('saleTotal').textContent = '0.00';
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    });
}