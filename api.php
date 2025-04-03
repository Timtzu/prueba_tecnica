<?php
include 'includes/db_connect.php';


if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    switch ($_GET['action']) {
        case 'get_products':
            $result = $conn->query("SELECT * FROM productos");
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            echo json_encode($products);
            break;

        case 'get_product':
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM productos WHERE id=$id");
            echo json_encode($result->fetch_assoc());
            break;

        case 'get_sales':
            $result = $conn->query("SELECT * FROM ventas ORDER BY fecha DESC");
            $sales = [];
            while ($row = $result->fetch_assoc()) {
                $sales[] = $row;
            }
            echo json_encode($sales);
            break;

        case 'get_sale_details':
            $id = $_GET['id'];
            $sql = "SELECT p.nombre, d.cantidad, d.subtotal FROM detalle_ventas d JOIN productos p ON d.producto_id = p.id WHERE d.venta_id=$id";
            $result = $conn->query($sql);
            $details = [];
            while ($row = $result->fetch_assoc()) {
                $details[] = $row;
            }
            echo json_encode($details);
            break;
    }
    $conn->close();
    exit;
}
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                $nombre = $_POST['nombre'];
                $precio = $_POST['precio'];
                $stock = $_POST['stock'];
                $estatus = $stock > 0 ? 'disponible' : 'agotado';
                $sql = "INSERT INTO productos (nombre, precio, stock, estatus) VALUES ('$nombre', $precio, $stock, '$estatus')";
                echo json_encode($conn->query($sql) === TRUE ? ["success" => true] : ["success" => false, "error" => $conn->error]);
                break;

            case 'edit_product':
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $precio = $_POST['precio'];
                $stock = $_POST['stock'];
                $estatus = $stock > 0 ? 'disponible' : 'agotado';
                $sql = "UPDATE productos SET nombre='$nombre', precio=$precio, stock=$stock, estatus='$estatus' WHERE id=$id";
                echo json_encode($conn->query($sql) === TRUE ? ["success" => true] : ["success" => false, "error" => $conn->error]);
                break;

            case 'delete_product':
                $id = $_POST['id'];
                $sql = "DELETE FROM productos WHERE id=$id";
                echo json_encode($conn->query($sql) === TRUE ? ["success" => true] : ["success" => false, "error" => $conn->error]);
                break;

            case 'record_sale':
                $items = json_decode($_POST['items'], true);
                $total = 0;
                $conn->begin_transaction();
                try {
                    $sql = "INSERT INTO ventas (total) VALUES (0)";
                    $conn->query($sql);
                    $venta_id = $conn->insert_id;

                    foreach ($items as $item) {
                        $producto_id = $item['producto_id'];
                        $cantidad = $item['cantidad'];
                        $result = $conn->query("SELECT precio, stock FROM productos WHERE id=$producto_id");
                        $product = $result->fetch_assoc();
                        $precio = $product['precio'];
                        $stock = $product['stock'];
                        $subtotal = $precio * $cantidad;
                        $total += $subtotal;

                        if ($stock < $cantidad) {
                            throw new Exception("Stock insuficiente para el producto ID $producto_id");
                        }

                        $sql = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, subtotal) VALUES ($venta_id, $producto_id, $cantidad, $subtotal)";
                        $conn->query($sql);

                        $new_stock = $stock - $cantidad;
                        $estatus = $new_stock > 0 ? 'disponible' : 'agotado';
                        $sql = "UPDATE productos SET stock=$new_stock, estatus='$estatus' WHERE id=$producto_id";
                        $conn->query($sql);
                    }

                    $sql = "UPDATE ventas SET total=$total WHERE id=$venta_id";
                    $conn->query($sql);

                    $conn->commit();
                    echo json_encode(["success" => true]);
                } catch (Exception $e) {
                    $conn->rollback();
                    echo json_encode(["success" => false, "error" => $e->getMessage()]);
                }
                break;
        }
    }
    $conn->close();
    exit;
}
$conn->close();
?>
?>