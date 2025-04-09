-- Archivo de inserción de datos de muestra para la base de datos SpringGreen
-- Datos en español de España

-- Seleccionamos la base de datos
USE springgreen;

-- ==================================================
-- 1. Inserción de datos en Propiedades (Hoteles)
-- ==================================================
INSERT INTO propiedades (nombre, direccion, ciudad, pais, telefono, email) VALUES
('Hotel Mediterráneo', 'Paseo Marítimo 45', 'Barcelona', 'España', '+34 932 555 123', 'reservas@hotelmediterraneo.es'),
('Gran Hotel Miramar', 'Paseo de Reding 22', 'Málaga', 'España', '+34 952 603 000', 'info@granhotelmiramar.es'),
('Parador de Granada', 'Calle Real de la Alhambra', 'Granada', 'España', '+34 958 221 440', 'granada@parador.es'),
('Hotel Atlantis Palace', 'Avenida del Puerto 78', 'Valencia', 'España', '+34 963 120 456', 'recepcion@atlantispalace.es'),
('Posada El Camino', 'Calle Mayor 12', 'Toledo', 'España', '+34 925 678 901', 'reservas@posadaelcamino.es');

-- ==================================================
-- 2. Inserción de datos en Tipos de Habitación
-- ==================================================
INSERT INTO tipos_habitacion (nombre, descripcion) VALUES
('Individual', 'Habitación con una cama individual, perfecta para viajeros solitarios'),
('Doble Estándar', 'Habitación con una cama de matrimonio o dos camas individuales'),
('Doble Superior', 'Habitación amplia con cama de matrimonio y vistas preferentes'),
('Junior Suite', 'Habitación de lujo con zona de estar y cama doble'),
('Suite', 'Amplia suite con salón independiente, dormitorio y baño de lujo'),
('Suite Presidencial', 'La mejor suite del hotel con múltiples habitaciones y servicios exclusivos'),
('Familiar', 'Habitación amplia con espacio para 2 adultos y 2 niños');

-- ==================================================
-- 3. Inserción de datos en Habitaciones
-- ==================================================
INSERT INTO habitaciones (propiedad_id, tipo_habitacion_id, numero, capacidad, estado) VALUES
-- Hotel Mediterráneo
(1, 1, '101', 1, 'disponible'),
(1, 2, '102', 2, 'disponible'),
(1, 3, '103', 2, 'ocupada'),
(1, 4, '104', 3, 'disponible'),
(1, 5, '105', 4, 'mantenimiento'),
-- Gran Hotel Miramar
(2, 2, '201', 2, 'disponible'),
(2, 3, '202', 2, 'ocupada'),
(2, 4, '203', 3, 'limpieza'),
(2, 5, '204', 4, 'disponible'),
(2, 6, '205', 6, 'ocupada'),
-- Parador de Granada
(3, 1, '301', 1, 'disponible'),
(3, 2, '302', 2, 'disponible'),
(3, 3, '303', 2, 'ocupada'),
(3, 7, '304', 4, 'disponible'),
(3, 5, '305', 4, 'mantenimiento'),
-- Hotel Atlantis Palace
(4, 1, '401', 1, 'disponible'),
(4, 2, '402', 2, 'ocupada'),
(4, 3, '403', 2, 'disponible'),
(4, 4, '404', 3, 'limpieza'),
(4, 5, '405', 4, 'disponible'),
-- Posada El Camino
(5, 1, '501', 1, 'disponible'),
(5, 2, '502', 2, 'ocupada'),
(5, 3, '503', 2, 'disponible'),
(5, 7, '504', 4, 'disponible'),
(5, 2, '505', 2, 'mantenimiento');

-- ==================================================
-- 4. Inserción de datos en Huéspedes
-- ==================================================
INSERT INTO huespedes (nombre, apellidos, documento_identidad, email, telefono) VALUES
('Antonio', 'García Pérez', '12345678Z', 'antonio.garcia@gmail.com', '+34 654 123 456'),
('María', 'Fernández López', '87654321X', 'maria.fernandez@hotmail.com', '+34 678 765 432'),
('Javier', 'Martínez Sánchez', '23456789Y', 'javier.martinez@yahoo.es', '+34 612 345 678'),
('Carmen', 'González Rodríguez', '98765432W', 'carmen.gonzalez@gmail.com', '+34 634 987 654'),
('David', 'López García', '34567890V', 'david.lopez@outlook.com', '+34 645 234 567'),
('Laura', 'Ruiz Fernández', '45678901U', 'laura.ruiz@gmail.com', '+34 689 456 789'),
('Manuel', 'Sánchez Martínez', '56789012T', 'manuel.sanchez@hotmail.com', '+34 632 567 890'),
('Lucía', 'Jiménez González', '67890123S', 'lucia.jimenez@yahoo.es', '+34 678 678 901'),
('Pablo', 'Gómez Ruiz', '78901234R', 'pablo.gomez@gmail.com', '+34 654 789 012'),
('Ana', 'Díaz Sánchez', '89012345Q', 'ana.diaz@outlook.com', '+34 612 890 123');

-- ==================================================
-- 5. Inserción de datos en Reservas
-- ==================================================
INSERT INTO reservas (huesped_id, propiedad_id, habitacion_id, fecha_checkin, fecha_checkout, estado) VALUES
(1, 1, 3, '2025-04-10', '2025-04-15', 'confirmada'),
(2, 2, 7, '2025-04-12', '2025-04-16', 'confirmada'),
(3, 3, 13, '2025-04-15', '2025-04-20', 'confirmada'),
(4, 4, 17, '2025-04-18', '2025-04-22', 'confirmada'),
(5, 5, 22, '2025-04-20', '2025-04-25', 'confirmada'),
(6, 1, 2, '2025-05-01', '2025-05-05', 'pendiente'),
(7, 2, 6, '2025-05-05', '2025-05-08', 'pendiente'),
(8, 3, 12, '2025-05-10', '2025-05-15', 'pendiente'),
(9, 4, 18, '2025-05-15', '2025-05-20', 'pendiente'),
(10, 5, 23, '2025-05-20', '2025-05-25', 'pendiente'),
(1, 3, 11, '2025-06-01', '2025-06-10', 'pendiente'),
(2, 1, 4, '2025-06-05', '2025-06-08', 'pendiente'),
(3, 2, 9, '2025-06-10', '2025-06-15', 'pendiente'),
(4, 5, 21, '2025-06-15', '2025-06-20', 'pendiente'),
(5, 4, 19, '2025-06-20', '2025-06-25', 'pendiente');

-- ==================================================
-- 6. Inserción de datos en Tarifas
-- ==================================================
INSERT INTO tarifas (tipo_habitacion_id, temporada, precio, moneda) VALUES
(1, 'Baja', 65.00, 'EUR'),
(1, 'Media', 85.00, 'EUR'),
(1, 'Alta', 105.00, 'EUR'),
(2, 'Baja', 95.00, 'EUR'),
(2, 'Media', 125.00, 'EUR'),
(2, 'Alta', 155.00, 'EUR'),
(3, 'Baja', 120.00, 'EUR'),
(3, 'Media', 150.00, 'EUR'),
(3, 'Alta', 190.00, 'EUR'),
(4, 'Baja', 160.00, 'EUR'),
(4, 'Media', 195.00, 'EUR'),
(4, 'Alta', 250.00, 'EUR'),
(5, 'Baja', 220.00, 'EUR'),
(5, 'Media', 280.00, 'EUR'),
(5, 'Alta', 350.00, 'EUR'),
(6, 'Baja', 500.00, 'EUR'),
(6, 'Media', 650.00, 'EUR'),
(6, 'Alta', 850.00, 'EUR'),
(7, 'Baja', 150.00, 'EUR'),
(7, 'Media', 180.00, 'EUR'),
(7, 'Alta', 220.00, 'EUR');

-- ==================================================
-- 7. Inserción de datos en Facturas
-- ==================================================
INSERT INTO facturas (reserva_id, monto_total, estado, metodo_pago, fecha_pago) VALUES
(1, 525.00, 'pagada', 'tarjeta', '2025-04-08 14:30:00'),
(2, 600.00, 'pagada', 'transferencia', '2025-04-10 10:15:00'),
(3, 950.00, 'pagada', 'tarjeta', '2025-04-14 16:45:00'),
(4, 620.00, 'pagada', 'efectivo', '2025-04-18 12:00:00'),
(5, 500.00, 'pendiente', NULL, NULL),
(6, 380.00, 'pendiente', NULL, NULL),
(7, 450.00, 'pendiente', NULL, NULL),
(8, 750.00, 'pendiente', NULL, NULL),
(9, 760.00, 'pendiente', NULL, NULL),
(10, 500.00, 'pendiente', NULL, NULL);

-- 7.1 Inserción de datos en Detalles de Factura
INSERT INTO facturas_detalle (factura_id, descripcion, cantidad, precio_unitario, monto) VALUES
(1, 'Alojamiento - Doble Superior', 5, 95.00, 475.00),
(1, 'Servicio de lavandería', 1, 25.00, 25.00),
(1, 'Minibar', 1, 25.00, 25.00),
(2, 'Alojamiento - Junior Suite', 4, 150.00, 600.00),
(3, 'Alojamiento - Doble Superior', 5, 190.00, 950.00),
(4, 'Alojamiento - Doble Estándar', 4, 155.00, 620.00),
(5, 'Alojamiento - Doble Estándar', 5, 100.00, 500.00),
(6, 'Alojamiento - Doble Estándar', 4, 95.00, 380.00),
(7, 'Alojamiento - Doble Estándar', 3, 150.00, 450.00),
(8, 'Alojamiento - Doble Estándar', 5, 150.00, 750.00),
(9, 'Alojamiento - Doble Superior', 5, 152.00, 760.00),
(10, 'Alojamiento - Doble Superior', 5, 100.00, 500.00);

-- ==================================================
-- 8. Inserción de datos en Gestión de Inventario
-- ==================================================
INSERT INTO inventario (propiedad_id, nombre_item, categoria, cantidad, stock_minimo, ubicacion) VALUES
(1, 'Toallas blancas grandes', 'Textil', 200, 50, 'Almacén principal'),
(1, 'Toallas blancas pequeñas', 'Textil', 300, 75, 'Almacén principal'),
(1, 'Sábanas 150x200', 'Textil', 120, 30, 'Almacén principal'),
(1, 'Sábanas 90x190', 'Textil', 80, 20, 'Almacén principal'),
(1, 'Gel de baño', 'Amenities', 500, 100, 'Almacén secundario'),
(1, 'Champú', 'Amenities', 500, 100, 'Almacén secundario'),
(1, 'Jabón de manos', 'Amenities', 600, 150, 'Almacén secundario'),
(1, 'Papel higiénico', 'Productos papel', 1000, 200, 'Almacén secundario'),
(1, 'Almohadas', 'Textil', 100, 25, 'Almacén principal'),
(1, 'Bombillas LED', 'Mantenimiento', 50, 15, 'Taller mantenimiento'),
(2, 'Toallas blancas grandes', 'Textil', 300, 75, 'Almacén principal'),
(2, 'Toallas blancas pequeñas', 'Textil', 450, 100, 'Almacén principal'),
(2, 'Sábanas 150x200', 'Textil', 180, 45, 'Almacén principal'),
(2, 'Sábanas 90x190', 'Textil', 120, 30, 'Almacén principal'),
(2, 'Gel de baño premium', 'Amenities', 600, 150, 'Almacén secundario'),
(3, 'Toallas blancas grandes', 'Textil', 150, 40, 'Almacén principal'),
(3, 'Toallas blancas pequeñas', 'Textil', 200, 50, 'Almacén principal'),
(3, 'Sábanas 150x200', 'Textil', 90, 25, 'Almacén principal'),
(3, 'Gel de baño ecológico', 'Amenities', 300, 75, 'Almacén secundario'),
(3, 'Productos de limpieza', 'Limpieza', 100, 25, 'Almacén productos químicos');

-- ==================================================
-- 9. Inserción de datos en Gestión de Personal
-- ==================================================
INSERT INTO personal (propiedad_id, nombre, apellidos, posicion, departamento, salario, fecha_contratacion, estado) VALUES
(1, 'Miguel', 'Sánchez Torres', 'Director', 'Dirección', 4500.00, '2020-01-15', 'activo'),
(1, 'Elena', 'Martínez Gómez', 'Jefa de Recepción', 'Recepción', 2800.00, '2020-02-01', 'activo'),
(1, 'Carlos', 'García Navarro', 'Recepcionista', 'Recepción', 1800.00, '2020-03-10', 'activo'),
(1, 'Sara', 'López Fernández', 'Recepcionista', 'Recepción', 1800.00, '2020-04-05', 'activo'),
(1, 'Alberto', 'Rodríguez Pérez', 'Jefe de Cocina', 'Restauración', 3200.00, '2020-01-20', 'activo'),
(1, 'Isabel', 'González Martín', 'Gobernanta', 'Pisos', 2500.00, '2020-02-15', 'activo'),
(1, 'Francisco', 'Pérez Sanz', 'Camarero', 'Restauración', 1700.00, '2020-03-15', 'activo'),
(1, 'Raquel', 'Díaz Romero', 'Camarera de piso', 'Pisos', 1600.00, '2020-04-01', 'activo'),
(1, 'Javier', 'Moreno Serrano', 'Mantenimiento', 'Servicios Técnicos', 1900.00, '2020-02-10', 'activo'),
(1, 'Sandra', 'Alonso García', 'Camarera de piso', 'Pisos', 1600.00, '2020-03-20', 'vacaciones'),
(2, 'Luis', 'Vázquez Romero', 'Director', 'Dirección', 5000.00, '2019-06-15', 'activo'),
(2, 'Marta', 'Ruiz Navarro', 'Jefa de Recepción', 'Recepción', 3000.00, '2019-07-01', 'activo'),
(2, 'Pedro', 'Castro López', 'Recepcionista', 'Recepción', 1900.00, '2019-08-10', 'activo'),
(2, 'Lucía', 'Ortega Díaz', 'Jefa de Eventos', 'Eventos', 2800.00, '2019-09-01', 'activo'),
(2, 'Juan', 'Sanz Martín', 'Chef Ejecutivo', 'Restauración', 3800.00, '2019-06-20', 'activo'),
(3, 'Ana', 'Torres Vega', 'Directora', 'Dirección', 4800.00, '2018-03-15', 'activo'),
(3, 'Roberto', 'Medina Ortiz', 'Jefe de Recepción', 'Recepción', 2900.00, '2018-04-01', 'activo'),
(3, 'Cristina', 'López Sánchez', 'Recepcionista', 'Recepción', 1850.00, '2018-05-10', 'activo'),
(3, 'Daniel', 'García Martínez', 'Mantenimiento', 'Servicios Técnicos', 1950.00, '2018-04-15', 'activo'),
(3, 'María', 'Jiménez Ruiz', 'Gobernanta', 'Pisos', 2600.00, '2018-03-20', 'activo');

-- ==================================================
-- 10. Inserción de datos en Gestión de Eventos
-- ==================================================
INSERT INTO eventos (propiedad_id, nombre, descripcion, fecha_inicio, fecha_fin, ubicacion, capacidad, estado) VALUES
(1, 'Conferencia Anual de Marketing', 'Conferencia para profesionales del marketing', '2025-05-15 09:00:00', '2025-05-16 18:00:00', 'Salón Mediterráneo', 100, 'planificado'),
(1, 'Boda García-Fernández', 'Ceremonia y banquete de boda', '2025-06-20 12:00:00', '2025-06-20 23:59:00', 'Salón de Baile', 150, 'planificado'),
(1, 'Reunión Ejecutiva', 'Reunión de consejo directivo', '2025-04-25 10:00:00', '2025-04-25 16:00:00', 'Sala de Juntas', 15, 'planificado'),
(2, 'Congreso Internacional de Medicina', 'Congreso anual con ponentes internacionales', '2025-07-10 08:00:00', '2025-07-12 20:00:00', 'Centro de Convenciones', 300, 'planificado'),
(2, 'Gala Benéfica', 'Cena de gala para recaudación de fondos', '2025-05-30 19:00:00', '2025-05-30 23:59:00', 'Salón de Baile', 200, 'planificado'),
(3, 'Seminario de Historia', 'Seminario sobre la historia de la Alhambra', '2025-06-05 10:00:00', '2025-06-07 18:00:00', 'Sala de Conferencias', 50, 'planificado'),
(3, 'Exposición de Arte Local', 'Exposición de artistas granadinos', '2025-07-01 10:00:00', '2025-07-15 20:00:00', 'Galería de Arte', 80, 'planificado'),
(4, 'Festival Gastronómico', 'Degustación de cocina mediterránea', '2025-05-25 12:00:00', '2025-05-26 22:00:00', 'Terraza Atlántica', 120, 'planificado'),
(5, 'Retiro de Yoga', 'Fin de semana de yoga y meditación', '2025-06-12 14:00:00', '2025-06-14 16:00:00', 'Jardín Principal', 30, 'planificado'),
(5, 'Taller de Cocina Tradicional', 'Aprendizaje de recetas castellanas', '2025-05-18 11:00:00', '2025-05-18 15:00:00', 'Cocina de Eventos', 15, 'planificado');

-- ==================================================
-- 11. Inserción de datos en Gestión de Presupuestos
-- ==================================================
INSERT INTO presupuestos (propiedad_id, anio, mes, categoria, monto_asignado, monto_gastado) VALUES
(1, 2025, 4, 'Personal', 45000.00, 45000.00),
(1, 2025, 4, 'Suministros', 10000.00, 8500.00),
(1, 2025, 4, 'Mantenimiento', 5000.00, 3200.00),
(1, 2025, 4, 'Marketing', 3000.00, 2800.00),
(1, 2025, 5, 'Personal', 45000.00, 0.00),
(1, 2025, 5, 'Suministros', 12000.00, 0.00),
(1, 2025, 5, 'Mantenimiento', 6000.00, 0.00),
(1, 2025, 5, 'Marketing', 4000.00, 0.00),
(2, 2025, 4, 'Personal', 55000.00, 55000.00),
(2, 2025, 4, 'Suministros', 15000.00, 12000.00),
(2, 2025, 4, 'Mantenimiento', 8000.00, 6500.00),
(2, 2025, 4, 'Marketing', 5000.00, 4800.00),
(2, 2025, 5, 'Personal', 55000.00, 0.00),
(2, 2025, 5, 'Suministros', 16000.00, 0.00),
(3, 2025, 4, 'Personal', 40000.00, 40000.00),
(3, 2025, 4, 'Suministros', 9000.00, 8200.00),
(3, 2025, 4, 'Mantenimiento', 6000.00, 5500.00),
(3, 2025, 5, 'Personal', 40000.00, 0.00),
(3, 2025, 5, 'Suministros', 10000.00, 0.00),
(3, 2025, 5, 'Mantenimiento', 7000.00, 0.00);

-- ==================================================
-- 12. Inserción de datos en Gestión de Mantenimiento
-- ==================================================
INSERT INTO mantenimiento (propiedad_id, habitacion_id, descripcion, tipo, solicitante_id, responsable_id, estado, prioridad, fecha_inicio, fecha_finalizacion) VALUES
(1, 5, 'Reparación de aire acondicionado', 'Correctivo', 2, 9, 'en_proceso', 'alta', '2025-04-08 10:00:00', NULL),
(1, NULL, 'Mantenimiento preventivo de piscina', 'Preventivo', 1, 9, 'programado', 'media', '2025-04-15 09:00:00', NULL),
(1, 3, 'Cambio de bombilla fundida', 'Correctivo', 4, 9, 'completado', 'baja', '2025-04-05 14:30:00', '2025-04-05 15:00:00'),
(2, 10, 'Reparación de ducha con fugas', 'Correctivo', 11, 19, 'pendiente', 'media', NULL, NULL),
(2, NULL, 'Revisión de sistemas eléctricos', 'Preventivo', 11, 19, 'programado', 'alta', '2025-04-20 08:00:00', NULL),
(3, 15, 'Reparación de persiana atascada', 'Correctivo', 16, 19, 'completado', 'baja', '2025-04-02 11:00:00', '2025-04-02 12:30:00'),
(3, NULL, 'Mantenimiento de jardines', 'Preventivo', 16, 19, 'en_proceso', 'baja', '2025-04-07 07:00:00', NULL),
(4, NULL, 'Revisión de sistemas de calefacción', 'Preventivo', NULL, NULL, 'pendiente', 'media', NULL, NULL),
(5, 25, 'Reparación de cerradura defectuosa', 'Correctivo', NULL, NULL, 'pendiente', 'alta', NULL, NULL);

-- ==================================================
-- 13. Inserción de datos en Gestión de Limpieza
-- ==================================================
INSERT INTO limpieza (propiedad_id, habitacion_id, personal_id, fecha_programada, hora_inicio, hora_fin, estado, tipo_limpieza, notas) VALUES
(1, 1, 8, '2025-04-10', '10:00:00', '10:30:00', 'completada', 'rutinaria', 'Completada sin incidencias'),
(1, 2, 8, '2025-04-10', '10:45:00', '11:15:00', 'completada', 'rutinaria', 'Completada sin incidencias'),
(1, 3, 10, '2025-04-10', '11:30:00', '12:00:00', 'pendiente', 'cambio_huesped', 'Pendiente checkin nuevo huésped'),
(1, 4, 10, '2025-04-10', '12:15:00', '12:45:00', 'pendiente', 'rutinaria', NULL),
(1, 1, 8, '2025-04-11', '10:00:00', '10:30:00', 'pendiente', 'rutinaria', NULL),
(2, 6, 13, '2025-04-10', '09:30:00', '10:00:00', 'completada', 'rutinaria', 'Completada sin incidencias'),
(2, 7, 13, '2025-04-10', '10:15:00', '10:45:00', 'pendiente', 'cambio_huesped', 'Pendiente checkout'),
(2, 8, 13, '2025-04-10', '11:00:00', '11:30:00', 'programada', 'profunda', 'Limpieza profunda trimestral'),
(3, 11, 20, '2025-04-10', '09:00:00', '09:30:00', 'completada', 'rutinaria', 'Completada sin incidencias'),
(3, 12, 20, '2025-04-10', '09:45:00', '10:15:00', 'pendiente', 'rutinaria', NULL);

-- ==================================================
-- 14. Inserción de datos en Gestión de Canales de Venta
-- ==================================================
INSERT INTO canales_venta (nombre, tipo, comision, estado) VALUES
('Directo - Web', 'directo', 0.00, 'activo'),
('Directo - Teléfono', 'directo', 0.00, 'activo'),
('Booking.com', 'ota', 15.00, 'activo'),
('Expedia', 'ota', 14.00, 'activo'),
('Hotelbeds', 'mayorista', 10.00, 'activo'),
('El Corte Inglés Viajes', 'agencia', 10.00, 'activo'),
('Viajes El Corte Inglés', 'agencia', 10.00, 'activo'),
('Halcón Viajes', 'agencia', 8.00, 'activo'),
('TripAdvisor', 'ota', 12.00, 'activo'),
('Airbnb', 'ota', 13.00, 'inactivo');

-- Relación entre Reservas y Canales
INSERT INTO reservas_canales (reserva_id, canal_id, codigo_reserva_externo) VALUES
(1, 1, 'DIR-20250410-001'),
(2, 3, 'BK-98765432'),
(3, 4, 'EXP-456789'),
(4, 3, 'BK-87654321'),
(5, 2, 'DIR-20250420-002'),
(6, 1, 'DIR-20250501-003'),
(7, 3, 'BK-76543210'),
(8, 4, 'EXP-567890'),
(9, 5, 'HB-123456'),
(10, 6, 'ECI-789012');

-- ==================================================
-- 15. Inserción de datos en Gestión de Restaurante
-- ==================================================

-- Categorías del menú

-- Continuing from part 15. Inserción de datos en Gestión de Restaurante

-- Categorías del menú
INSERT INTO restaurante_categorias (nombre, descripcion) VALUES
('Entrantes', 'Platos para compartir y comenzar la comida'),
('Ensaladas', 'Opciones frescas y saludables'),
('Sopas', 'Caldos y cremas caseras'),
('Carnes', 'Selección de carnes de primera calidad'),
('Pescados', 'Pescados frescos del día'),
('Arroces', 'Paellas y otros arroces tradicionales'),
('Postres', 'Dulces caseros'),
('Bebidas', 'Refrescos, vinos y otras bebidas');

-- Menú del restaurante
INSERT INTO restaurante_menu (categoria_id, nombre, descripcion, precio, disponible) VALUES
(1, 'Jamón ibérico de bellota', 'Jamón ibérico de bellota con pan con tomate', 24.50, TRUE),
(1, 'Croquetas caseras', 'Croquetas caseras de jamón ibérico (6 uds)', 12.00, TRUE),
(1, 'Tabla de quesos', 'Selección de quesos nacionales con membrillo y frutos secos', 18.50, TRUE),
(2, 'Ensalada César', 'Lechuga romana, pollo, picatostes, parmesano y salsa César', 14.50, TRUE),
(2, 'Ensalada de la huerta', 'Tomate, pepino, cebolla, pimiento y aceitunas con vinagreta', 12.00, TRUE),
(3, 'Sopa de pescado', 'Sopa tradicional de pescado y marisco', 10.50, TRUE),
(3, 'Crema de calabaza', 'Crema de calabaza con semillas tostadas', 9.00, TRUE),
(4, 'Solomillo de ternera', 'Solomillo de ternera con patatas y verduras', 26.00, TRUE),
(4, 'Entrecot a la parrilla', 'Entrecot de vaca madurado 30 días a la parrilla', 24.00, TRUE),
(5, 'Lubina a la espalda', 'Lubina fresca a la plancha con verduras', 22.00, TRUE),
(5, 'Pulpo a la gallega', 'Pulpo cocido con patata y pimentón', 23.50, TRUE),
(6, 'Paella valenciana', 'Paella tradicional con pollo y conejo (mín. 2 personas)', 18.00, TRUE),
(6, 'Arroz negro', 'Arroz con sepia y su tinta (mín. 2 personas)', 19.00, TRUE),
(7, 'Tarta de queso casera', 'Tarta de queso casera con coulis de frutos rojos', 7.50, TRUE),
(7, 'Crema catalana', 'Crema catalana tradicional caramelizada', 6.50, TRUE),
(8, 'Agua mineral', 'Agua mineral (1L)', 3.00, TRUE),
(8, 'Refresco', 'Coca-Cola, Fanta, etc.', 3.50, TRUE),
(8, 'Copa de vino tinto', 'Copa de vino tinto Ribera del Duero', 4.50, TRUE);

-- Mesas del restaurante
INSERT INTO restaurante_mesas (propiedad_id, nombre, ubicacion, capacidad, estado) VALUES
(1, 'Mesa 1', 'Terraza', 4, 'disponible'),
(1, 'Mesa 2', 'Terraza', 4, 'disponible'),
(1, 'Mesa 3', 'Terraza', 2, 'ocupada'),
(1, 'Mesa 4', 'Interior', 6, 'disponible'),
(1, 'Mesa 5', 'Interior', 8, 'reservada'),
(1, 'Mesa 6', 'Interior', 2, 'disponible'),
(2, 'Mesa 1', 'Terraza mar', 4, 'ocupada'),
(2, 'Mesa 2', 'Terraza mar', 4, 'disponible'),
(2, 'Mesa 3', 'Restaurante principal', 6, 'disponible'),
(2, 'Mesa 4', 'Restaurante principal', 8, 'reservada'),
(2, 'Mesa 5', 'Restaurante principal', 2, 'disponible'),
(3, 'Mesa 1', 'Patio', 4, 'disponible'),
(3, 'Mesa 2', 'Patio', 4, 'ocupada'),
(3, 'Mesa 3', 'Salón', 6, 'disponible'),
(3, 'Mesa 4', 'Salón', 8, 'disponible');

-- Reservas de mesas
INSERT INTO restaurante_reservas (mesa_id, huesped_id, reserva_id, fecha, hora_inicio, hora_fin, num_comensales, estado, notas) VALUES
(5, 1, 1, '2025-04-12', '20:30:00', '22:30:00', 6, 'confirmada', 'Preferencia cerca de ventana'),
(10, 2, 2, '2025-04-14', '21:00:00', '23:00:00', 8, 'confirmada', 'Celebración de aniversario'),
(5, 4, NULL, '2025-04-15', '14:00:00', '16:00:00', 6, 'confirmada', 'Menú especial solicitado'),
(10, 5, NULL, '2025-04-18', '21:30:00', '23:30:00', 8, 'pendiente', NULL),
(3, 6, NULL, '2025-04-20', '13:30:00', '15:30:00', 2, 'confirmada', NULL);

-- Pedidos en el restaurante
INSERT INTO restaurante_pedidos (mesa_id, reserva_mesa_id, camarero_id, total, estado, fecha_hora) VALUES
(3, NULL, 7, 85.50, 'servido', '2025-04-09 13:30:00'),
(7, NULL, 13, 127.00, 'pagado', '2025-04-09 14:00:00'),
(13, NULL, 19, 67.50, 'en_preparacion', '2025-04-09 14:15:00'),
(5, 1, 7, 0.00, 'pendiente', '2025-04-12 20:30:00'),
(10, 2, 13, 0.00, 'pendiente', '2025-04-14 21:00:00');

-- Detalle de los pedidos del restaurante
INSERT INTO restaurante_pedidos_detalle (pedido_id, menu_id, cantidad, precio_unitario, notas, estado) VALUES
(1, 2, 1, 12.00, NULL, 'servido'),
(1, 4, 2, 14.50, 'Sin pollo en una', 'servido'),
(1, 8, 1, 26.00, 'Término medio', 'servido'),
(1, 17, 3, 3.50, NULL, 'servido'),
(1, 14, 1, 7.50, NULL, 'servido'),
(2, 1, 1, 24.50, NULL, 'servido'),
(2, 5, 1, 12.00, NULL, 'servido'),
(2, 10, 2, 22.00, NULL, 'servido'),
(2, 15, 2, 6.50, NULL, 'servido'),
(2, 18, 2, 4.50, NULL, 'servido'),
(2, 16, 1, 3.00, NULL, 'servido'),
(3, 3, 1, 18.50, NULL, 'en_preparacion'),
(3, 7, 1, 9.00, NULL, 'listo'),
(3, 9, 1, 24.00, 'Poco hecho', 'en_preparacion'),
(3, 17, 2, 3.50, NULL, 'servido'),
(3, 14, 1, 7.50, NULL, 'pendiente');

-- ==================================================
-- 16. Inserción de datos en Servicios Adicionales
-- ==================================================
INSERT INTO servicios (propiedad_id, nombre, descripcion, precio, disponible) VALUES
(1, 'Masaje relajante', 'Masaje corporal de 50 minutos', 80.00, TRUE),
(1, 'Acceso al spa', 'Acceso a todas las instalaciones del spa durante 2 horas', 30.00, TRUE),
(1, 'Clase de yoga', 'Clase de yoga de 60 minutos', 25.00, TRUE),
(1, 'Servicio de lavandería', 'Servicio de lavado y planchado de ropa', 15.00, TRUE),
(1, 'Traslado aeropuerto', 'Servicio de transporte al aeropuerto', 45.00, TRUE),
(2, 'Masaje terapéutico', 'Masaje terapéutico de 60 minutos', 95.00, TRUE),
(2, 'Tratamiento facial', 'Tratamiento facial hidratante', 75.00, TRUE),
(2, 'Circuito termal', 'Acceso al circuito termal durante 2 horas', 40.00, TRUE),
(2, 'Servicio de niñera', 'Servicio de cuidado de niños por hora', 25.00, TRUE),
(2, 'Tour guiado', 'Tour guiado por la ciudad (3 horas)', 60.00, TRUE),
(3, 'Visita guiada Alhambra', 'Visita guiada a la Alhambra con entradas incluidas', 70.00, TRUE),
(3, 'Clase de flamenco', 'Clase de iniciación al baile flamenco', 40.00, TRUE),
(3, 'Cata de vinos', 'Degustación de vinos locales', 35.00, TRUE),
(4, 'Alquiler de bicicletas', 'Alquiler de bicicleta por día', 15.00, TRUE),
(4, 'Excursión en barco', 'Excursión en barco por la costa (2 horas)', 50.00, TRUE),
(5, 'Ruta de senderismo', 'Ruta guiada por los alrededores', 30.00, TRUE),
(5, 'Experiencia gastronómica', 'Taller de cocina tradicional', 45.00, TRUE);

-- Solicitudes de servicios por huéspedes
INSERT INTO servicios_solicitudes (reserva_id, servicio_id, personal_id, fecha_solicitud, fecha_servicio, estado, precio_aplicado, notas) VALUES
(1, 1, 6, '2025-04-09 15:30:00', '2025-04-13 16:00:00', 'confirmado', 80.00, 'Preferencia por masajista femenina'),
(1, 4, 8, '2025-04-09 15:35:00', '2025-04-12 10:00:00', 'completado', 15.00, '3 camisas, 2 pantalones'),
(2, 8, 13, '2025-04-09 10:00:00', '2025-04-14 11:00:00', 'confirmado', 40.00, 'Dos personas'),
(2, 10, 14, '2025-04-09 10:15:00', '2025-04-15 09:00:00', 'confirmado', 60.00, 'Tour en inglés'),
(3, 11, 17, '2025-04-09 16:00:00', '2025-04-17 10:00:00', 'confirmado', 70.00, 'Tour en español para 2 personas'),
(4, 14, NULL, '2025-04-09 14:00:00', '2025-04-19 09:00:00', 'solicitado', 15.00, 'Solicitud de 2 bicicletas'),
(5, 17, NULL, '2025-04-09 11:30:00', '2025-04-22 17:00:00', 'solicitado', 45.00, 'Taller para 2 personas');
