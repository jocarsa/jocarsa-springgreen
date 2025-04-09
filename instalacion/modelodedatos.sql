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
