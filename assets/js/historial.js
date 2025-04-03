function loadSales() {
    fetch('api.php?action=get_sales')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('salesTable').getElementsByTagName('tbody')[0];
            tbody.innerHTML = '';
            data.forEach(sale => {
                const row = `<tr>
                    <td>${sale.id}</td>
                    <td>${sale.fecha}</td>
                    <td>${sale.total}</td>
                    <td><button class="btn btn-sm btn-info" onclick="viewSaleDetails(${sale.id})">Detalles</button></td>
                </tr>`;
                tbody.innerHTML += row;
            });
        })
        .catch(error => console.error('Error loading sales:', error));
}

function viewSaleDetails(id) {
    fetch(`api.php?action=get_sale_details&id=${id}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('saleDetailsTable');
            tbody.innerHTML = '';
            data.forEach(detail => {
                tbody.innerHTML += `<tr>
                    <td>${detail.nombre}</td>
                    <td>${detail.cantidad}</td>
                    <td>${detail.subtotal}</td>
                </tr>`;
            });
            if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
                console.error('Bootstrap Modal is not available');
                return;
            }
            new bootstrap.Modal(document.getElementById('saleDetailsModal')).show();
        })
        .catch(error => console.error('Error viewing sale details:', error));
}