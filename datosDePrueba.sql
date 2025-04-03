CREATE DATABASE prueba_tecnica;
USE prueba_tecnica;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    stock INT,
    estatus ENUM('disponible', 'agotado') DEFAULT 'disponible'
);

CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2)
);

CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT,
    producto_id INT,
    cantidad INT,
    subtotal DECIMAL(10,2),
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

INSERT INTO productos (nombre, precio, stock, estatus) VALUES
('Laptop HP', 12000.50, 10, 'disponible'),
('Mouse Logitech', 350.75, 5, 'disponible'),
('Teclado Mecánico', 850.00, 0, 'agotado'),
('Monitor Samsung', 4500.99, 15, 'disponible'),
('Impresora Epson', 3200.00, 2, 'disponible');