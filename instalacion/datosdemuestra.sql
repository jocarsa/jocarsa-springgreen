-- ==================================================
-- 1. TABLA: propiedades
-- ==================================================
INSERT INTO propiedades (nombre_propiedad, direccion, ciudad, pais, telefono, email)
VALUES
('Hotel Sol y Mar', 'Calle Principal 123', 'Cartagena', 'Colombia', '+57 3001234567', 'contacto@solymar.com'),
('Hostal Luna', 'Av. Central 45', 'Quito', 'Ecuador', '+593 9987654321', 'info@hostalluna.ec');

-- ==================================================
-- 2. TABLA: tipos_habitacion
-- ==================================================
INSERT INTO tipos_habitacion (nombre_tipo, descripcion)
VALUES
('Habitación Estándar', 'Cama doble, baño privado, aire acondicionado.'),
('Suite Familiar', 'Habitación amplia con sala, dos camas dobles y vista al mar.');

-- ==================================================
-- 3. TABLA: habitaciones
-- ==================================================
-- Suponiendo que:
--   propiedad_id=1 => "Hotel Sol y Mar"
--   propiedad_id=2 => "Hostal Luna"
--   tipo_habitacion_id=1 => "Habitación Estándar"
--   tipo_habitacion_id=2 => "Suite Familiar"

INSERT INTO habitaciones (propiedad_id, tipo_habitacion_id, numero_habitacion, capacidad, estado)
VALUES
(1, 1, '101', 2, 'disponible'),
(1, 2, '201', 4, 'disponible'),
(2, 1, '01', 2, 'mantenimiento'),
(2, 1, '02', 2, 'disponible');

-- ==================================================
-- 4. TABLA: huespedes
-- ==================================================
INSERT INTO huespedes (nombre, apellidos, documento_identidad, email, telefono)
VALUES
('Carlos', 'Pérez', 'CP123456', 'carlos.perez@example.com', '+57 3011112222'),
('Andrea', 'Gómez', 'AG987654', 'andrea.gomez@example.com', '+593 998765432');

-- ==================================================
-- 5. TABLA: reservas
-- ==================================================
-- Suponemos que:
--   huesped_id=1 => "Carlos Pérez"
--   huesped_id=2 => "Andrea Gómez"
--   propiedad_id=1 => "Hotel Sol y Mar"
--   propiedad_id=2 => "Hostal Luna"

INSERT INTO reservas (huesped_id, propiedad_id, fecha_checkin, fecha_checkout, estado)
VALUES
(1, 1, '2025-06-10', '2025-06-15', 'confirmada'),
(2, 1, '2025-07-01', '2025-07-05', 'pendiente');

-- ==================================================
-- 6. TABLA: tarifas
-- ==================================================
-- Suponemos:
--   tipo_habitacion_id=1 => "Habitación Estándar"
--   tipo_habitacion_id=2 => "Suite Familiar"

INSERT INTO tarifas (tipo_habitacion_id, temporada, precio, moneda)
VALUES
(1, 'Temporada Baja', 50.00, 'USD'),
(2, 'Temporada Alta', 120.00, 'USD');

-- ==================================================
-- 7. TABLAS: facturas y facturas_detalle
-- ==================================================
-- Vamos a crear una factura asociada a la reserva con reserva_id=1
INSERT INTO facturas (reserva_id, monto_total, estado)
VALUES
(1, 200.00, 'pendiente');

-- Suponiendo la factura generada es factura_id=1, añadimos detalles:
INSERT INTO facturas_detalle (factura_id, descripcion, monto)
VALUES
(1, 'Alojamiento 5 noches', 150.00),
(1, 'Servicio de bar', 50.00);

-- ==================================================
-- 8. TABLA: inventario
-- ==================================================
INSERT INTO inventario (propiedad_id, nombre_item, cantidad, ubicacion)
VALUES
(1, 'Toallas de Baño', 50, 'Almacén Principal'),
(2, 'Sábanas', 30, 'Cuarto de Limpieza');

-- ==================================================
-- 9. TABLA: personal
-- ==================================================
INSERT INTO personal (propiedad_id, nombre, apellidos, posicion, salario, fecha_contratacion)
VALUES
(1, 'Lucía', 'Martínez', 'Recepcionista', 800.00, '2025-01-10'),
(2, 'Javier', 'Rojas', 'Encargado de Limpieza', 600.00, '2024-12-01');

-- ==================================================
-- 10. TABLA: eventos
-- ==================================================
INSERT INTO eventos (propiedad_id, nombre_evento, fecha_inicio, fecha_fin, estado)
VALUES
(1, 'Conferencia de Turismo', '2025-08-01 09:00:00', '2025-08-01 18:00:00', 'planificado');

-- ==================================================
-- 11. TABLA: presupuestos
-- ==================================================
INSERT INTO presupuestos (propiedad_id, anio, monto_total)
VALUES
(1, 2025, 50000.00),
(2, 2025, 20000.00);

-- ==================================================
-- 12. TABLA: mantenimiento
-- ==================================================
-- Suponemos habitacion_id=2 es la "201" en Hotel Sol y Mar
INSERT INTO mantenimiento (propiedad_id, habitacion_id, descripcion_incidencia, estado, prioridad)
VALUES
(1, 2, 'Fuga de agua en el baño', 'en proceso', 'alta'),
(1, NULL, 'Pintura de zonas comunes', 'pendiente', 'media');

-- ==================================================
-- 13. TABLA: limpieza
-- ==================================================
-- Suponemos personal_id=2 => "Javier Rojas" (trabaja en la propiedad_id=2, ¡ojo con la consistencia!)
-- Pero para el ejemplo, forzamos un registro en propiedad_id=1
-- Si deseas consistencia total, cambia la propiedad del empleado.
INSERT INTO limpieza (propiedad_id, habitacion_id, personal_id, fecha_tarea, estado, notas)
VALUES
(1, 1, 1, '2025-06-09', 'finalizada', 'Preparar habitación 101 para el check-in'),
(1, 2, 1, '2025-06-10', 'pendiente', 'Revisión general antes de la llegada del huésped');

-- ==================================================
-- 14. TABLAS: canales_venta y reservas_canales
-- ==================================================
INSERT INTO canales_venta (nombre_canal, comision)
VALUES
('Booking.com', 15.00),
('Expedia', 12.00);

-- Vincular la reserva con ID=2 a Booking.com
INSERT INTO reservas_canales (reserva_id, canal_id)
VALUES
(2, 1);

-- ==================================================
-- 15. Gestión de Restaurante
-- ==================================================

-- Mesas
INSERT INTO restaurante_mesas (propiedad_id, nombre_mesa, capacidad)
VALUES
(1, 'Mesa 1', 4),
(1, 'Mesa 2', 2);

-- Pedidos
-- Suponemos mesa_id=1 => "Mesa 1"
-- reserva_id=1 => "Carlos Pérez"
INSERT INTO restaurante_pedidos (mesa_id, reserva_id, total, estado_pedido)
VALUES
(1, 1, 0.00, 'pendiente');

-- Menú
INSERT INTO restaurante_menu (nombre_plato, precio, descripcion)
VALUES
('Arroz con mariscos', 10.00, 'Delicioso arroz preparado con frutos del mar'),
('Limonada', 2.50, 'Refresco de limón natural');

-- Detalle de pedidos
-- Supongamos el pedido_id=1
--   menu_id=1 => 'Arroz con mariscos'
--   menu_id=2 => 'Limonada'
INSERT INTO restaurante_pedidos_detalle (pedido_id, menu_id, cantidad, precio_item)
VALUES
(1, 1, 2, 10.00),  -- 2 platos de arroz con mariscos
(1, 2, 2, 2.50);   -- 2 limonadas

-- Actualizamos el total del pedido (opcional, dependiendo de la lógica de tu app)
UPDATE restaurante_pedidos
SET total = (SELECT SUM(cantidad * precio_item)
             FROM restaurante_pedidos_detalle
             WHERE restaurante_pedidos_detalle.pedido_id = restaurante_pedidos.pedido_id)
WHERE pedido_id = 1;

