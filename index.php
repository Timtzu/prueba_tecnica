<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba Técnica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
<div class="container mt-5">
        <h1 class="mb-4">Gestión de Productos y Ventas</h1>

        <!-- Product Section -->
        <h3>Productos</h3>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Agregar Producto</button>
        <table class="table table-striped" id="productsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <!-- Record Sale Section -->
        <h3>Registrar Venta</h3>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#recordSaleModal">Registrar Venta</button>

        <!-- Sales History Section -->
        <h3>Historial de Ventas</h3>
        <table class="table table-striped" id="salesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addNombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="addNombre">
                    </div>
                    <div class="mb-3">
                        <label for="addPrecio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="addPrecio" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="addStock" class="form-label">Stock</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="decrementValue('addStock', 0, validateQuantity)">-1</button>
                            <input type="number" class="form-control" id="addStock" step="1" min="0" value="0" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="incrementValue('addStock', 0, validateQuantity)">+1</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="addProduct()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="mb-3">
                        <label for="editNombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editNombre">
                    </div>
                    <div class="mb-3">
                        <label for="editPrecio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="editPrecio" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="editStock" class="form-label">Stock</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="decrementValue('editStock', 0, validateQuantity)">-1</button>
                            <input type="number" class="form-control" id="editStock" step="1" min="0" value="0" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="incrementValue('editStock', 0, validateQuantity)">+1</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="updateProduct()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Sale Modal -->
    <div class="modal fade" id="recordSaleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="saleItemsTable"></tbody>
                    </table>
                    <button class="btn btn-secondary" onclick="addSaleItemRow()">Agregar Producto</button>
                    <div class="mt-3">
                        <strong>Total: <span id="saleTotal">0.00</span></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="recordSale()">Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Details Modal -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="saleDetailsTable"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js" integrity="sha384-YUe2LzesAfftltw+PEaao2tjU/QATaW/rOitAq67e0CT0Zi2VVRL0oC4+gAaeBKu" crossorigin="anonymous"></script>s
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/dependencias/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="assets/js/productos.js"></script>
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/ventas.js"></script>
    <script src="assets/js/historial.js"></script>
    <script>
        window.onload = () => {
            loadProducts();
            loadProductsForSale();
            loadSales();
        };
    </script>
</body>
</html>