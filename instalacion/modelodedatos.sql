-- Creamos la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS springgreen 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Seleccionamos la base de datos
USE springgreen;

-- ==================================================
-- 1. Tabla de Propiedades
-- ==================================================
CREATE TABLE IF NOT EXISTS propiedades (
    propiedad_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_propiedad VARCHAR(255) NOT NULL,
    direccion VARCHAR(255),
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
    nombre_tipo VARCHAR(100) NOT NULL,
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
    numero_habitacion VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL DEFAULT 1,
    estado VARCHAR(50) DEFAULT 'disponible',
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
    fecha_checkin DATE NOT NULL,
    fecha_checkout DATE NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente', 
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservas_huespedes
        FOREIGN KEY (huesped_id) REFERENCES huespedes(huesped_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_reservas_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
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
    moneda VARCHAR(10) DEFAULT 'USD',
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
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_facturas_reservas
        FOREIGN KEY (reserva_id) REFERENCES reservas(reserva_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- 7.1 Detalles de Factura
CREATE TABLE IF NOT EXISTS facturas_detalle (
    factura_detalle_id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    descripcion VARCHAR(255),
    monto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
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
    cantidad INT NOT NULL DEFAULT 0,
    ubicacion VARCHAR(100),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_inventario_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==================================================
-- 9. Gestión de Personal (Recursos Humanos)
-- ==================================================
CREATE TABLE IF NOT EXISTS personal (
    personal_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    posicion VARCHAR(100),
    salario DECIMAL(10,2) DEFAULT 0.00,
    fecha_contratacion DATE,
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
    nombre_evento VARCHAR(255) NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME,
    estado VARCHAR(50) DEFAULT 'planificado',
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
    monto_total DECIMAL(10,2) NOT NULL,
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
    descripcion_incidencia TEXT NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente',
    prioridad VARCHAR(50) DEFAULT 'media',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mantenimiento_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_mantenimiento_habitaciones
        FOREIGN KEY (habitacion_id) REFERENCES habitaciones(habitacion_id)
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
    fecha_tarea DATE NOT NULL,
    estado VARCHAR(50) DEFAULT 'pendiente',
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
    nombre_canal VARCHAR(100) NOT NULL,
    comision DECIMAL(5,2) DEFAULT 0.00,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
);

-- 14.1 Relación entre Reservas y Canales
CREATE TABLE IF NOT EXISTS reservas_canales (
    reserva_id INT NOT NULL,
    canal_id INT NOT NULL,
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

-- Mesas del restaurante
CREATE TABLE IF NOT EXISTS restaurante_mesas (
    mesa_id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    nombre_mesa VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurante_mesas_propiedades
        FOREIGN KEY (propiedad_id) REFERENCES propiedades(propiedad_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Pedidos en el restaurante
CREATE TABLE IF NOT EXISTS restaurante_pedidos (
    pedido_id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT NOT NULL,
    reserva_id INT DEFAULT NULL,
    total DECIMAL(10,2) DEFAULT 0.00,
    estado_pedido VARCHAR(50) DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurante_pedidos_mesas
        FOREIGN KEY (mesa_id) REFERENCES restaurante_mesas(mesa_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_restaurante_pedidos_reservas
        FOREIGN KEY (reserva_id) REFERENCES reservas(reserva_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Menú del restaurante
CREATE TABLE IF NOT EXISTS restaurante_menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_plato VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Detalle de los pedidos del restaurante
CREATE TABLE IF NOT EXISTS restaurante_pedidos_detalle (
    pedido_detalle_id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    menu_id INT NOT NULL,
    cantidad INT DEFAULT 1,
    precio_item DECIMAL(10,2) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurante_pedidos_detalle_pedidos
        FOREIGN KEY (pedido_id) REFERENCES restaurante_pedidos(pedido_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_restaurante_pedidos_detalle_menu
        FOREIGN KEY (menu_id) REFERENCES restaurante_menu(menu_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Fin del modelo de datos en español

