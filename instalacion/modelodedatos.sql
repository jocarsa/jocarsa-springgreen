-- Creamos la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS springgreen 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Seleccionamos la base de datos
USE springgreen;

-- ==================================================
-- 1. Tabla de Propiedades (Hoteles)
-- ==================================================
CREATE TABLE IF NOT EXISTS propiedades (
    propiedad_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    ciudad VARCHAR(100),
    pais VARCHAR(100),
    telefono VARCHAR(50),
    email VARCHAR(100),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
);

-- ==================================================
-- 2. Tabla de Tipos de Habitación
-- ==================================================
CREATE TABLE IF NOT EXISTS tipos_habitacion (
    tipo_habitacion_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
);

-- ==================================================
-- 3. Tabla de Habitaciones
-- ==================================================
CREATE TABLE IF NOT EXISTS habitaciones (
    habitacion_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    tipo_habitacion_id INT NOT NULL,
    numero VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL DEFAULT 1,
    estado VARCHAR(50) DEFAULT 'disponible', -- disponible, ocupada, mantenimiento, limpieza
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_habitaciones_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_habitaciones_tipos_habitacion
        FOREIGN KEY (tipo_habitacion_id) REFERENCES tipos_habitacion(tipo_habitacion_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 4. Tabla de Huéspedes
-- ==================================================
CREATE TABLE IF NOT EXISTS huespedes (
    huesped_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    documento_identidad VARCHAR(50),
    email VARCHAR(100),
    telefono VARCHAR(50),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
);

-- ==================================================
-- 5. Tabla de Reservas
-- ==================================================
CREATE TABLE IF NOT EXISTS reservas (
    reserva_id INT AUTO_INCREMENT PRIMARY KEY,
    huesped_id INT NOT NULL,
    propiedad_id INT NOT NULL,
    habitacion_id INT NOT NULL,
    fecha_checkin DATE NOT NULL,
    fecha_checkout DATE NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente', -- pendiente, confirmada, cancelada, completada
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservas_huespedes
        FOREIGN KEY (huesped_id) REFERENCES huespedes(huesped_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_reservas_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_reservas_habitaciones
        FOREIGN KEY (habitacion_id) REFERENCES habitaciones(habitacion_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 6. Tabla de Tarifas
-- ==================================================
CREATE TABLE IF NOT EXISTS tarifas (
    tarifa_id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_habitacion_id INT NOT NULL,
    temporada VARCHAR(100),
    precio DECIMAL(10,2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'EUR',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tarifas_tipos_habitacion
        FOREIGN KEY (tipo_habitacion_id) REFERENCES tipos_habitacion(tipo_habitacion_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 7. Tabla de Facturas
-- ==================================================
CREATE TABLE IF NOT EXISTS facturas (
    factura_id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    monto_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente', -- pendiente, pagada, anulada
    metodo_pago VARCHAR(50),
    fecha_pago DATETIME,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_facturas_reservas
        FOREIGN KEY (reserva_id) REFERENCES reservas(reserva_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- 7.1 Detalles de Factura
CREATE TABLE IF NOT EXISTS facturas_detalle (
    detalle_id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_facturas_detalle_facturas
        FOREIGN KEY (factura_id) REFERENCES facturas(factura_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 8. Gestión de Inventario
-- ==================================================
CREATE TABLE IF NOT EXISTS inventario (
    inventario_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    nombre_item VARCHAR(100) NOT NULL,
    categoria VARCHAR(100),
    cantidad INT NOT NULL DEFAULT 0,
    stock_minimo INT DEFAULT 0,
    ubicacion VARCHAR(100),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_inventario_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 9. Gestión de Personal
-- ==================================================
CREATE TABLE IF NOT EXISTS personal (
    personal_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    posicion VARCHAR(100),
    departamento VARCHAR(100),
    salario DECIMAL(10,2) DEFAULT 0.00,
    fecha_contratacion DATE,
    estado VARCHAR(50) DEFAULT 'activo', -- activo, inactivo, vacaciones
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_personal_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 10. Gestión de Eventos
-- ==================================================
CREATE TABLE IF NOT EXISTS eventos (
    evento_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    ubicacion VARCHAR(100),
    capacidad INT,
    estado VARCHAR(50) DEFAULT 'planificado', -- planificado, en_curso, finalizado, cancelado
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_eventos_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 11. Gestión de Presupuestos
-- ==================================================
CREATE TABLE IF NOT EXISTS presupuestos (
    presupuesto_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    anio INT NOT NULL,
    mes INT,
    categoria VARCHAR(100),
    monto_asignado DECIMAL(10,2) NOT NULL,
    monto_gastado DECIMAL(10,2) DEFAULT 0.00,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_presupuestos_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 12. Gestión de Mantenimiento
-- ==================================================
CREATE TABLE IF NOT EXISTS mantenimiento (
    mantenimiento_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    habitacion_id INT DEFAULT NULL,
    descripcion TEXT NOT NULL,
    tipo VARCHAR(100),
    solicitante_id INT,
    responsable_id INT,
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente', -- pendiente, en_proceso, completado, cancelado
    prioridad VARCHAR(50) DEFAULT 'media', -- baja, media, alta, urgente
    fecha_inicio DATETIME,
    fecha_finalizacion DATETIME,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mantenimiento_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_mantenimiento_habitaciones
        FOREIGN KEY (habitacion_id) REFERENCES habitaciones(habitacion_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_mantenimiento_solicitante
        FOREIGN KEY (solicitante_id) REFERENCES personal(personal_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_mantenimiento_responsable
        FOREIGN KEY (responsable_id) REFERENCES personal(personal_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- ==================================================
-- 13. Gestión de Limpieza
-- ==================================================
CREATE TABLE IF NOT EXISTS limpieza (
    limpieza_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    habitacion_id INT NOT NULL,
    personal_id INT NOT NULL,
    fecha_programada DATE NOT NULL,
    hora_inicio TIME,
    hora_fin TIME,
    estado VARCHAR(50) DEFAULT 'pendiente', -- pendiente, en_proceso, completada, verificada
    tipo_limpieza VARCHAR(50) DEFAULT 'rutinaria', -- rutinaria, profunda, cambio_huesped
    notas TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_limpieza_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_limpieza_habitaciones
        FOREIGN KEY (habitacion_id) REFERENCES habitaciones(habitacion_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_limpieza_personal
        FOREIGN KEY (personal_id) REFERENCES personal(personal_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 14. Gestión de Canales de Venta
-- ==================================================
CREATE TABLE IF NOT EXISTS canales_venta (
    canal_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50), -- directo, ota, agencia
    comision DECIMAL(5,2) DEFAULT 0.00,
    estado VARCHAR(50) DEFAULT 'activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
);

-- Relación entre Reservas y Canales
CREATE TABLE IF NOT EXISTS reservas_canales (
    reserva_id INT NOT NULL,
    canal_id INT NOT NULL,
    codigo_reserva_externo VARCHAR(100),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (reserva_id, canal_id),
    CONSTRAINT fk_reservas_canales_reservas
        FOREIGN KEY (reserva_id) REFERENCES reservas(reserva_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_reservas_canales_canales_venta
        FOREIGN KEY (canal_id) REFERENCES canales_venta(canal_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 15. Gestión de Restaurante
-- ==================================================

-- Categorías del menú
CREATE TABLE IF NOT EXISTS restaurante_categorias (
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
);

-- Menú del restaurante
CREATE TABLE IF NOT EXISTS restaurante_menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurante_menu_categorias
        FOREIGN KEY (categoria_id) REFERENCES restaurante_categorias(categoria_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Mesas del restaurante
CREATE TABLE IF NOT EXISTS restaurante_mesas (
    mesa_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    ubicacion VARCHAR(100),
    capacidad INT NOT NULL,
    estado VARCHAR(50) DEFAULT 'disponible', -- disponible, ocupada, reservada, mantenimiento
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurante_mesas_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Reservas de mesas
CREATE TABLE IF NOT EXISTS restaurante_reservas (
    reserva_mesa_id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT NOT NULL,
    huesped_id INT,
    reserva_id INT,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    num_comensales INT NOT NULL DEFAULT 1,
    estado VARCHAR(50) DEFAULT 'confirmada', -- confirmada, cancelada, completada, no_show
    notas TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurante_reservas_mesas
        FOREIGN KEY (mesa_id) REFERENCES restaurante_mesas(mesa_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_restaurante_reservas_huespedes
        FOREIGN KEY (huesped_id) REFERENCES huespedes(huesped_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_restaurante_reservas_reservas
        FOREIGN KEY (reserva_id) REFERENCES reservas(reserva_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Pedidos en el restaurante
CREATE TABLE IF NOT EXISTS restaurante_pedidos (
    pedido_id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT NOT NULL,
    reserva_mesa_id INT,
    camarero_id INT,
    total DECIMAL(10,2) DEFAULT 0.00,
    estado VARCHAR(50) DEFAULT 'pendiente', -- pendiente, en_preparacion, servido, pagado, cancelado
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurante_pedidos_mesas
        FOREIGN KEY (mesa_id) REFERENCES restaurante_mesas(mesa_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_restaurante_pedidos_reservas_mesas
        FOREIGN KEY (reserva_mesa_id) REFERENCES restaurante_reservas(reserva_mesa_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_restaurante_pedidos_camareros
        FOREIGN KEY (camarero_id) REFERENCES personal(personal_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Detalle de los pedidos del restaurante
CREATE TABLE IF NOT EXISTS restaurante_pedidos_detalle (
    detalle_id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    menu_id INT NOT NULL,
    cantidad INT DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    notas TEXT,
    estado VARCHAR(50) DEFAULT 'pendiente', -- pendiente, en_preparacion, listo, servido, cancelado
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurante_pedidos_detalle_pedidos
        FOREIGN KEY (pedido_id) REFERENCES restaurante_pedidos(pedido_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_restaurante_pedidos_detalle_menu
        FOREIGN KEY (menu_id) REFERENCES restaurante_menu(menu_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 16. Servicios Adicionales
-- ==================================================
CREATE TABLE IF NOT EXISTS servicios (
    servicio_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_servicios_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Solicitudes de servicios por huéspedes
CREATE TABLE IF NOT EXISTS servicios_solicitudes (
    solicitud_id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    servicio_id INT NOT NULL,
    personal_id INT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_servicio DATETIME,
    estado VARCHAR(50) DEFAULT 'solicitado', -- solicitado, confirmado, en_proceso, completado, cancelado
    precio_aplicado DECIMAL(10,2) NOT NULL,
    notas TEXT,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_servicios_solicitudes_reservas
        FOREIGN KEY (reserva_id) REFERENCES reservas(reserva_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_servicios_solicitudes_servicios
        FOREIGN KEY (servicio_id) REFERENCES servicios(servicio_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_servicios_solicitudes_personal
        FOREIGN KEY (personal_id) REFERENCES personal(personal_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Create table for users (usuarios)
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Agregar comentarios a las tablas y columnas (en español)

-- Tabla de Propiedades (Hoteles)
ALTER TABLE propiedades COMMENT 'Almacena información de los hoteles o propiedades del grupo. Cada registro representa una propiedad física distinta.';
ALTER TABLE propiedades MODIFY COLUMN propiedad_id INT AUTO_INCREMENT COMMENT 'Identificador único de la propiedad';
ALTER TABLE propiedades MODIFY COLUMN nombre VARCHAR(255) NOT NULL COMMENT 'Nombre comercial del hotel o propiedad';
ALTER TABLE propiedades MODIFY COLUMN direccion VARCHAR(255) NOT NULL COMMENT 'Dirección física completa del establecimiento';
ALTER TABLE propiedades MODIFY COLUMN ciudad VARCHAR(100) COMMENT 'Ciudad donde está ubicada la propiedad';
ALTER TABLE propiedades MODIFY COLUMN pais VARCHAR(100) COMMENT 'País donde está ubicada la propiedad';
ALTER TABLE propiedades MODIFY COLUMN telefono VARCHAR(50) COMMENT 'Número de teléfono principal de contacto';
ALTER TABLE propiedades MODIFY COLUMN email VARCHAR(100) COMMENT 'Correo electrónico de contacto de la propiedad';
ALTER TABLE propiedades MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se registró la propiedad en el sistema';
ALTER TABLE propiedades MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última modificación del registro';

-- Tabla de Tipos de Habitación
ALTER TABLE tipos_habitacion COMMENT 'Catálogo de categorías de habitaciones disponibles en el sistema (ej: Individual, Doble, Suite)';
ALTER TABLE tipos_habitacion MODIFY COLUMN tipo_habitacion_id INT AUTO_INCREMENT COMMENT 'Identificador único del tipo de habitación';
ALTER TABLE tipos_habitacion MODIFY COLUMN nombre VARCHAR(100) NOT NULL COMMENT 'Nombre descriptivo del tipo de habitación';
ALTER TABLE tipos_habitacion MODIFY COLUMN descripcion TEXT COMMENT 'Descripción detallada de las características de este tipo de habitación';
ALTER TABLE tipos_habitacion MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE tipos_habitacion MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Habitaciones
ALTER TABLE habitaciones COMMENT 'Registro de cada habitación física individual disponible en las propiedades';
ALTER TABLE habitaciones MODIFY COLUMN habitacion_id INT AUTO_INCREMENT COMMENT 'Identificador único de la habitación';
ALTER TABLE habitaciones MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Hotel o propiedad a la que pertenece esta habitación';
ALTER TABLE habitaciones MODIFY COLUMN tipo_habitacion_id INT NOT NULL COMMENT 'Tipo o categoría de esta habitación';
ALTER TABLE habitaciones MODIFY COLUMN numero VARCHAR(50) NOT NULL COMMENT 'Número o identificador visible de la habitación para huéspedes';
ALTER TABLE habitaciones MODIFY COLUMN capacidad INT NOT NULL DEFAULT 1 COMMENT 'Número máximo de personas que pueden alojarse en esta habitación';
ALTER TABLE habitaciones MODIFY COLUMN estado VARCHAR(50) DEFAULT 'disponible' COMMENT 'Estado actual de la habitación: disponible, ocupada, mantenimiento o limpieza';
ALTER TABLE habitaciones MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE habitaciones MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Huéspedes
ALTER TABLE huespedes COMMENT 'Registro de clientes que se han alojado o tienen reservas en nuestras propiedades';
ALTER TABLE huespedes MODIFY COLUMN huesped_id INT AUTO_INCREMENT COMMENT 'Identificador único del huésped';
ALTER TABLE huespedes MODIFY COLUMN nombre VARCHAR(100) NOT NULL COMMENT 'Nombre(s) del huésped';
ALTER TABLE huespedes MODIFY COLUMN apellidos VARCHAR(100) NOT NULL COMMENT 'Apellido(s) del huésped';
ALTER TABLE huespedes MODIFY COLUMN documento_identidad VARCHAR(50) COMMENT 'Número de documento oficial de identidad (DNI, pasaporte, etc.)';
ALTER TABLE huespedes MODIFY COLUMN email VARCHAR(100) COMMENT 'Correo electrónico de contacto';
ALTER TABLE huespedes MODIFY COLUMN telefono VARCHAR(50) COMMENT 'Número de teléfono de contacto';
ALTER TABLE huespedes MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de registro en el sistema';
ALTER TABLE huespedes MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Reservas
ALTER TABLE reservas COMMENT 'Gestiona las reservas de habitaciones realizadas por los huéspedes';
ALTER TABLE reservas MODIFY COLUMN reserva_id INT AUTO_INCREMENT COMMENT 'Identificador único de la reserva';
ALTER TABLE reservas MODIFY COLUMN huesped_id INT NOT NULL COMMENT 'Cliente que realiza la reserva';
ALTER TABLE reservas MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Hotel donde se realiza la reserva';
ALTER TABLE reservas MODIFY COLUMN habitacion_id INT NOT NULL COMMENT 'Habitación específica asignada a la reserva';
ALTER TABLE reservas MODIFY COLUMN fecha_checkin DATE NOT NULL COMMENT 'Fecha prevista de entrada del huésped';
ALTER TABLE reservas MODIFY COLUMN fecha_checkout DATE NOT NULL COMMENT 'Fecha prevista de salida del huésped';
ALTER TABLE reservas MODIFY COLUMN estado VARCHAR(50) NOT NULL DEFAULT 'pendiente' COMMENT 'Estado actual: pendiente, confirmada, cancelada o completada';
ALTER TABLE reservas MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación de la reserva';
ALTER TABLE reservas MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última modificación';

-- Tabla de Tarifas
ALTER TABLE tarifas COMMENT 'Define los precios para cada tipo de habitación según temporada';
ALTER TABLE tarifas MODIFY COLUMN tarifa_id INT AUTO_INCREMENT COMMENT 'Identificador único de la tarifa';
ALTER TABLE tarifas MODIFY COLUMN tipo_habitacion_id INT NOT NULL COMMENT 'Tipo de habitación al que aplica esta tarifa';
ALTER TABLE tarifas MODIFY COLUMN temporada VARCHAR(100) COMMENT 'Temporada a la que aplica (ej: alta, baja, navidad, verano)';
ALTER TABLE tarifas MODIFY COLUMN precio DECIMAL(10,2) NOT NULL COMMENT 'Precio por noche para este tipo de habitación y temporada';
ALTER TABLE tarifas MODIFY COLUMN moneda VARCHAR(10) DEFAULT 'EUR' COMMENT 'Código de moneda (por defecto EUR para euro)';
ALTER TABLE tarifas MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE tarifas MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Facturas
ALTER TABLE facturas COMMENT 'Registro de facturas emitidas por reservas y servicios';
ALTER TABLE facturas MODIFY COLUMN factura_id INT AUTO_INCREMENT COMMENT 'Identificador único de la factura';
ALTER TABLE facturas MODIFY COLUMN reserva_id INT NOT NULL COMMENT 'Reserva asociada a esta factura';
ALTER TABLE facturas MODIFY COLUMN monto_total DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Importe total de la factura';
ALTER TABLE facturas MODIFY COLUMN estado VARCHAR(50) NOT NULL DEFAULT 'pendiente' COMMENT 'Estado de pago: pendiente, pagada o anulada';
ALTER TABLE facturas MODIFY COLUMN metodo_pago VARCHAR(50) COMMENT 'Método utilizado para el pago (efectivo, tarjeta, transferencia, etc.)';
ALTER TABLE facturas MODIFY COLUMN fecha_pago DATETIME COMMENT 'Fecha y hora en que se realizó el pago';
ALTER TABLE facturas MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de emisión de la factura';
ALTER TABLE facturas MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última modificación';

-- Tabla de Detalles de Factura
ALTER TABLE facturas_detalle COMMENT 'Detalle de conceptos incluidos en cada factura';
ALTER TABLE facturas_detalle MODIFY COLUMN detalle_id INT AUTO_INCREMENT COMMENT 'Identificador único del concepto facturado';
ALTER TABLE facturas_detalle MODIFY COLUMN factura_id INT NOT NULL COMMENT 'Factura a la que pertenece este detalle';
ALTER TABLE facturas_detalle MODIFY COLUMN descripcion VARCHAR(255) NOT NULL COMMENT 'Descripción del concepto facturado';
ALTER TABLE facturas_detalle MODIFY COLUMN cantidad INT NOT NULL DEFAULT 1 COMMENT 'Cantidad de unidades del concepto';
ALTER TABLE facturas_detalle MODIFY COLUMN precio_unitario DECIMAL(10,2) NOT NULL COMMENT 'Precio por unidad del concepto';
ALTER TABLE facturas_detalle MODIFY COLUMN monto DECIMAL(10,2) NOT NULL COMMENT 'Importe total de este concepto (cantidad × precio unitario)';
ALTER TABLE facturas_detalle MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';

-- Tabla de Inventario
ALTER TABLE inventario COMMENT 'Control de artículos en inventario para cada propiedad';
ALTER TABLE inventario MODIFY COLUMN inventario_id INT AUTO_INCREMENT COMMENT 'Identificador único del ítem de inventario';
ALTER TABLE inventario MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Propiedad a la que pertenece este inventario';
ALTER TABLE inventario MODIFY COLUMN nombre_item VARCHAR(100) NOT NULL COMMENT 'Nombre del artículo inventariado';
ALTER TABLE inventario MODIFY COLUMN categoria VARCHAR(100) COMMENT 'Categoría del artículo (ej: limpieza, amenities, mantenimiento)';
ALTER TABLE inventario MODIFY COLUMN cantidad INT NOT NULL DEFAULT 0 COMMENT 'Cantidad actual disponible';
ALTER TABLE inventario MODIFY COLUMN stock_minimo INT DEFAULT 0 COMMENT 'Nivel mínimo de stock antes de reordenar';
ALTER TABLE inventario MODIFY COLUMN ubicacion VARCHAR(100) COMMENT 'Ubicación física del artículo dentro de la propiedad';
ALTER TABLE inventario MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE inventario MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Personal
ALTER TABLE personal COMMENT 'Registro de empleados que trabajan en las propiedades';
ALTER TABLE personal MODIFY COLUMN personal_id INT AUTO_INCREMENT COMMENT 'Identificador único del empleado';
ALTER TABLE personal MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Propiedad donde trabaja principalmente';
ALTER TABLE personal MODIFY COLUMN nombre VARCHAR(100) NOT NULL COMMENT 'Nombre(s) del empleado';
ALTER TABLE personal MODIFY COLUMN apellidos VARCHAR(100) NOT NULL COMMENT 'Apellido(s) del empleado';
ALTER TABLE personal MODIFY COLUMN posicion VARCHAR(100) COMMENT 'Cargo o puesto que ocupa';
ALTER TABLE personal MODIFY COLUMN departamento VARCHAR(100) COMMENT 'Departamento al que pertenece (recepción, limpieza, mantenimiento, etc.)';
ALTER TABLE personal MODIFY COLUMN salario DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Salario base del empleado';
ALTER TABLE personal MODIFY COLUMN fecha_contratacion DATE COMMENT 'Fecha de inicio de la relación laboral';
ALTER TABLE personal MODIFY COLUMN estado VARCHAR(50) DEFAULT 'activo' COMMENT 'Estado actual: activo, inactivo o vacaciones';
ALTER TABLE personal MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE personal MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Eventos
ALTER TABLE eventos COMMENT 'Gestión de eventos y actividades organizadas en las propiedades';
ALTER TABLE eventos MODIFY COLUMN evento_id INT AUTO_INCREMENT COMMENT 'Identificador único del evento';
ALTER TABLE eventos MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Propiedad donde se realiza el evento';
ALTER TABLE eventos MODIFY COLUMN nombre VARCHAR(255) NOT NULL COMMENT 'Nombre o título del evento';
ALTER TABLE eventos MODIFY COLUMN descripcion TEXT COMMENT 'Descripción detallada del evento';
ALTER TABLE eventos MODIFY COLUMN fecha_inicio DATETIME NOT NULL COMMENT 'Fecha y hora de inicio del evento';
ALTER TABLE eventos MODIFY COLUMN fecha_fin DATETIME NOT NULL COMMENT 'Fecha y hora de finalización del evento';
ALTER TABLE eventos MODIFY COLUMN ubicacion VARCHAR(100) COMMENT 'Localización específica dentro de la propiedad';
ALTER TABLE eventos MODIFY COLUMN capacidad INT COMMENT 'Número máximo de asistentes';
ALTER TABLE eventos MODIFY COLUMN estado VARCHAR(50) DEFAULT 'planificado' COMMENT 'Estado actual: planificado, en_curso, finalizado o cancelado';
ALTER TABLE eventos MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE eventos MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Presupuestos
ALTER TABLE presupuestos COMMENT 'Control de presupuestos asignados y gastos por categoría';
ALTER TABLE presupuestos MODIFY COLUMN presupuesto_id INT AUTO_INCREMENT COMMENT 'Identificador único del presupuesto';
ALTER TABLE presupuestos MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Propiedad a la que aplica este presupuesto';
ALTER TABLE presupuestos MODIFY COLUMN anio INT NOT NULL COMMENT 'Año fiscal del presupuesto';
ALTER TABLE presupuestos MODIFY COLUMN mes INT COMMENT 'Mes específico (1-12) si el presupuesto es mensual';
ALTER TABLE presupuestos MODIFY COLUMN categoria VARCHAR(100) COMMENT 'Categoría presupuestaria (personal, marketing, mantenimiento, etc.)';
ALTER TABLE presupuestos MODIFY COLUMN monto_asignado DECIMAL(10,2) NOT NULL COMMENT 'Cantidad presupuestada para el período';
ALTER TABLE presupuestos MODIFY COLUMN monto_gastado DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cantidad efectivamente gastada hasta el momento';
ALTER TABLE presupuestos MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE presupuestos MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Mantenimiento
ALTER TABLE mantenimiento COMMENT 'Gestión de tareas de mantenimiento preventivo y correctivo';
ALTER TABLE mantenimiento MODIFY COLUMN mantenimiento_id INT AUTO_INCREMENT COMMENT 'Identificador único de la tarea de mantenimiento';
ALTER TABLE mantenimiento MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Propiedad donde se realiza el mantenimiento';
ALTER TABLE mantenimiento MODIFY COLUMN habitacion_id INT DEFAULT NULL COMMENT 'Habitación específica si aplica';
ALTER TABLE mantenimiento MODIFY COLUMN descripcion TEXT NOT NULL COMMENT 'Descripción detallada del problema o tarea';
ALTER TABLE mantenimiento MODIFY COLUMN tipo VARCHAR(100) COMMENT 'Tipo de mantenimiento (preventivo, correctivo, mejora)';
ALTER TABLE mantenimiento MODIFY COLUMN solicitante_id INT COMMENT 'Empleado que reportó o solicitó el mantenimiento';
ALTER TABLE mantenimiento MODIFY COLUMN responsable_id INT COMMENT 'Empleado asignado para realizar la tarea';
ALTER TABLE mantenimiento MODIFY COLUMN estado VARCHAR(50) NOT NULL DEFAULT 'pendiente' COMMENT 'Estado actual: pendiente, en_proceso, completado o cancelado';
ALTER TABLE mantenimiento MODIFY COLUMN prioridad VARCHAR(50) DEFAULT 'media' COMMENT 'Nivel de urgencia: baja, media, alta o urgente';
ALTER TABLE mantenimiento MODIFY COLUMN fecha_inicio DATETIME COMMENT 'Fecha y hora en que se comenzó a trabajar';
ALTER TABLE mantenimiento MODIFY COLUMN fecha_finalizacion DATETIME COMMENT 'Fecha y hora en que se completó la tarea';
ALTER TABLE mantenimiento MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE mantenimiento MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Limpieza
ALTER TABLE limpieza COMMENT 'Programación y control de tareas de limpieza de habitaciones';
ALTER TABLE limpieza MODIFY COLUMN limpieza_id INT AUTO_INCREMENT COMMENT 'Identificador único de la tarea de limpieza';
ALTER TABLE limpieza MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Propiedad donde se realiza la limpieza';
ALTER TABLE limpieza MODIFY COLUMN habitacion_id INT NOT NULL COMMENT 'Habitación que debe limpiarse';
ALTER TABLE limpieza MODIFY COLUMN personal_id INT NOT NULL COMMENT 'Empleado asignado para realizar la limpieza';
ALTER TABLE limpieza MODIFY COLUMN fecha_programada DATE NOT NULL COMMENT 'Fecha para la que está programada la limpieza';
ALTER TABLE limpieza MODIFY COLUMN hora_inicio TIME COMMENT 'Hora en que comenzó la limpieza';
ALTER TABLE limpieza MODIFY COLUMN hora_fin TIME COMMENT 'Hora en que finalizó la limpieza';
ALTER TABLE limpieza MODIFY COLUMN estado VARCHAR(50) DEFAULT 'pendiente' COMMENT 'Estado actual: pendiente, en_proceso, completada o verificada';
ALTER TABLE limpieza MODIFY COLUMN tipo_limpieza VARCHAR(50) DEFAULT 'rutinaria' COMMENT 'Tipo: rutinaria, profunda o cambio_huesped';
ALTER TABLE limpieza MODIFY COLUMN notas TEXT COMMENT 'Observaciones o instrucciones especiales';
ALTER TABLE limpieza MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE limpieza MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Canales de Venta
ALTER TABLE canales_venta COMMENT 'Catálogo de canales de distribución para reservas';
ALTER TABLE canales_venta MODIFY COLUMN canal_id INT AUTO_INCREMENT COMMENT 'Identificador único del canal';
ALTER TABLE canales_venta MODIFY COLUMN nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del canal de venta (web propia, Booking, Expedia, etc.)';
ALTER TABLE canales_venta MODIFY COLUMN tipo VARCHAR(50) COMMENT 'Categoría: directo, OTA (Online Travel Agency) o agencia tradicional';
ALTER TABLE canales_venta MODIFY COLUMN comision DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de comisión que cobra el canal por reserva';
ALTER TABLE canales_venta MODIFY COLUMN estado VARCHAR(50) DEFAULT 'activo' COMMENT 'Estado actual del canal: activo o inactivo';
ALTER TABLE canales_venta MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE canales_venta MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Relación entre Reservas y Canales
ALTER TABLE reservas_canales COMMENT 'Vincula cada reserva con su canal de distribución correspondiente';
ALTER TABLE reservas_canales MODIFY COLUMN reserva_id INT NOT NULL COMMENT 'Identificador de la reserva';
ALTER TABLE reservas_canales MODIFY COLUMN canal_id INT NOT NULL COMMENT 'Canal por el que se realizó la reserva';
ALTER TABLE reservas_canales MODIFY COLUMN codigo_reserva_externo VARCHAR(100) COMMENT 'Código o identificador de la reserva en el sistema externo';
ALTER TABLE reservas_canales MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';

-- Tabla de Categorías de Restaurante
ALTER TABLE restaurante_categorias COMMENT 'Categorías de productos del menú del restaurante';
ALTER TABLE restaurante_categorias MODIFY COLUMN categoria_id INT AUTO_INCREMENT COMMENT 'Identificador único de la categoría';
ALTER TABLE restaurante_categorias MODIFY COLUMN nombre VARCHAR(100) NOT NULL COMMENT 'Nombre de la categoría (entrantes, principales, postres, etc.)';
ALTER TABLE restaurante_categorias MODIFY COLUMN descripcion TEXT COMMENT 'Descripción de la categoría';
ALTER TABLE restaurante_categorias MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE restaurante_categorias MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Menú del Restaurante
ALTER TABLE restaurante_menu COMMENT 'Productos y platos disponibles en el restaurante';
ALTER TABLE restaurante_menu MODIFY COLUMN menu_id INT AUTO_INCREMENT COMMENT 'Identificador único del producto o plato';
ALTER TABLE restaurante_menu MODIFY COLUMN categoria_id INT NOT NULL COMMENT 'Categoría a la que pertenece este plato';
ALTER TABLE restaurante_menu MODIFY COLUMN nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del plato o producto';
ALTER TABLE restaurante_menu MODIFY COLUMN descripcion TEXT COMMENT 'Descripción detallada, ingredientes, alérgenos, etc.';
ALTER TABLE restaurante_menu MODIFY COLUMN precio DECIMAL(10,2) NOT NULL COMMENT 'Precio de venta al público';
ALTER TABLE restaurante_menu MODIFY COLUMN disponible BOOLEAN DEFAULT TRUE COMMENT 'Indica si el plato está disponible actualmente';
ALTER TABLE restaurante_menu MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE restaurante_menu MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Mesas del Restaurante
ALTER TABLE restaurante_mesas COMMENT 'Registro de mesas disponibles en el restaurante';
ALTER TABLE restaurante_mesas MODIFY COLUMN mesa_id INT AUTO_INCREMENT COMMENT 'Identificador único de la mesa';
ALTER TABLE restaurante_mesas MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Propiedad a la que pertenece esta mesa';
ALTER TABLE restaurante_mesas MODIFY COLUMN nombre VARCHAR(50) NOT NULL COMMENT 'Nombre o número identificativo de la mesa';
ALTER TABLE restaurante_mesas MODIFY COLUMN ubicacion VARCHAR(100) COMMENT 'Localización dentro del restaurante (terraza, interior, etc.)';
ALTER TABLE restaurante_mesas MODIFY COLUMN capacidad INT NOT NULL COMMENT 'Número máximo de comensales que pueden sentarse';
ALTER TABLE restaurante_mesas MODIFY COLUMN estado VARCHAR(50) DEFAULT 'disponible' COMMENT 'Estado actual: disponible, ocupada, reservada o mantenimiento';
ALTER TABLE restaurante_mesas MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE restaurante_mesas MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Reservas de Mesas
ALTER TABLE restaurante_reservas COMMENT 'Gestión de reservas de mesas en el restaurante';
ALTER TABLE restaurante_reservas MODIFY COLUMN reserva_mesa_id INT AUTO_INCREMENT COMMENT 'Identificador único de la reserva de mesa';
ALTER TABLE restaurante_reservas MODIFY COLUMN mesa_id INT NOT NULL COMMENT 'Mesa reservada';
ALTER TABLE restaurante_reservas MODIFY COLUMN huesped_id INT COMMENT 'Cliente que realiza la reserva (si está alojado)';
ALTER TABLE restaurante_reservas MODIFY COLUMN reserva_id INT COMMENT 'Reserva de habitación asociada (si procede)';
ALTER TABLE restaurante_reservas MODIFY COLUMN fecha DATE NOT NULL COMMENT 'Fecha para la que se realiza la reserva';
ALTER TABLE restaurante_reservas MODIFY COLUMN hora_inicio TIME NOT NULL COMMENT 'Hora de inicio de la reserva';
ALTER TABLE restaurante_reservas MODIFY COLUMN hora_fin TIME NOT NULL COMMENT 'Hora prevista de finalización';
ALTER TABLE restaurante_reservas MODIFY COLUMN num_comensales INT NOT NULL DEFAULT 1 COMMENT 'Número de personas que acudirán';
ALTER TABLE restaurante_reservas MODIFY COLUMN estado VARCHAR(50) DEFAULT 'confirmada' COMMENT 'Estado: confirmada, cancelada, completada o no_show';
ALTER TABLE restaurante_reservas MODIFY COLUMN notas TEXT COMMENT 'Observaciones o peticiones especiales';
ALTER TABLE restaurante_reservas MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE restaurante_reservas MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Pedidos en Restaurante
ALTER TABLE restaurante_pedidos COMMENT 'Registro de pedidos realizados en el restaurante';
ALTER TABLE restaurante_pedidos MODIFY COLUMN pedido_id INT AUTO_INCREMENT COMMENT 'Identificador único del pedido';
ALTER TABLE restaurante_pedidos MODIFY COLUMN mesa_id INT NOT NULL COMMENT 'Mesa que realiza el pedido';
ALTER TABLE restaurante_pedidos MODIFY COLUMN reserva_mesa_id INT COMMENT 'Reserva asociada si existiera';
ALTER TABLE restaurante_pedidos MODIFY COLUMN camarero_id INT COMMENT 'Empleado que toma y atiende el pedido';
ALTER TABLE restaurante_pedidos MODIFY COLUMN total DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Importe total del pedido';
ALTER TABLE restaurante_pedidos MODIFY COLUMN estado VARCHAR(50) DEFAULT 'pendiente' COMMENT 'Estado: pendiente, en_preparacion, servido, pagado o cancelado';
ALTER TABLE restaurante_pedidos MODIFY COLUMN fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se realizó el pedido';
ALTER TABLE restaurante_pedidos MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Detalle de Pedidos
ALTER TABLE restaurante_pedidos_detalle COMMENT 'Detalle de productos incluidos en cada pedido del restaurante';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN detalle_id INT AUTO_INCREMENT COMMENT 'Identificador único de la línea de pedido';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN pedido_id INT NOT NULL COMMENT 'Pedido al que pertenece esta línea';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN menu_id INT NOT NULL COMMENT 'Producto o plato solicitado';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN cantidad INT DEFAULT 1 COMMENT 'Cantidad solicitada de este producto';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN precio_unitario DECIMAL(10,2) NOT NULL COMMENT 'Precio unitario aplicado';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN notas TEXT COMMENT 'Observaciones específicas (punto de cocción, sin ingredientes, etc.)';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN estado VARCHAR(50) DEFAULT 'pendiente' COMMENT 'Estado: pendiente, en_preparacion, listo, servido o cancelado';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE restaurante_pedidos_detalle MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Servicios Adicionales
ALTER TABLE servicios COMMENT 'Catálogo de servicios extra ofrecidos por las propiedades';
ALTER TABLE servicios MODIFY COLUMN servicio_id INT AUTO_INCREMENT COMMENT 'Identificador único del servicio';
ALTER TABLE servicios MODIFY COLUMN propiedad_id INT NOT NULL COMMENT 'Propiedad que ofrece este servicio';
ALTER TABLE servicios MODIFY COLUMN nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del servicio (spa, gimnasio, traslados, etc.)';
ALTER TABLE servicios MODIFY COLUMN descripcion TEXT COMMENT 'Descripción detallada del servicio ofrecido';
ALTER TABLE servicios MODIFY COLUMN precio DECIMAL(10,2) NOT NULL COMMENT 'Precio base del servicio';
ALTER TABLE servicios MODIFY COLUMN disponible BOOLEAN DEFAULT TRUE COMMENT 'Indica si el servicio está disponible actualmente';
ALTER TABLE servicios MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro';
ALTER TABLE servicios MODIFY COLUMN fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la última actualización';

-- Tabla de Solicitudes de Servicios
ALTER TABLE servicios_solicitudes COMMENT 'Registro de servicios adicionales solicitados por huéspedes';
ALTER TABLE servicios_solicitudes MODIFY COLUMN solicitud_id INT AUTO_INCREMENT COMMENT 'Identificador único de la solicitud';
ALTER TABLE servicios_solicitudes MODIFY COLUMN reserva_id INT NOT NULL COMMENT 'Reserva que solicita el servicio';
ALTER TABLE servicios_solicitudes MODIFY COLUMN servicio_id INT NOT NULL COMMENT 'Servicio solicitado';
ALTER TABLE servicios_solicitudes MODIFY COLUMN personal_id INT COMMENT 'Empleado asignado para atender el servicio';
ALTER TABLE servicios_solicitudes MODIFY COLUMN fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se realizó la solicitud';
ALTER TABLE servicios_solicitudes MODIFY COLUMN fecha_servicio DATETIME COMMENT 'Fecha y hora programada para realizar el servicio';
ALTER TABLE servicios_solicitudes MODIFY COLUMN estado VARCHAR(50) DEFAULT 'solicitado' COMMENT 'Estado: solicitado, confirmado, en_proceso, completado o cancelado';
ALTER TABLE servicios_solicitudes MODIFY COLUMN precio_aplicado DECIMAL(10,2) NOT NULL COMMENT 'Precio final aplicado al servicio';

