<?php
// index.php
session_start();

// Para simplificar, conexión a la BD aquí (lo ideal es un archivo separado)
include "config.php";
$mysqli = new mysqli($host, $user, $pass, $dbName);
if ($mysqli->connect_errno) {
    die("Error de conexión MySQL: " . $mysqli->connect_error);
}

// Definimos las secciones
$secciones = [
    'propiedades' => 'Propiedades',
    'tipos_habitacion' => 'Tipos de Habitación',
    'habitaciones' => 'Habitaciones',
    'huespedes' => 'Huéspedes',
    'reservas' => 'Reservas',
    'tarifas' => 'Tarifas',
    'facturas' => 'Facturas',
    'facturas_detalle' => 'Detalle Facturas',
    'inventario' => 'Inventario',
    'personal' => 'Personal',
    'eventos' => 'Eventos',
    'presupuestos' => 'Presupuestos',
    'mantenimiento' => 'Mantenimiento',
    'limpieza' => 'Limpieza',
    'canales_venta' => 'Canales de Venta',
    'reservas_canales' => 'Rel. Reservas-Canales',
    'restaurante_mesas' => 'Rest. Mesas',
    'restaurante_pedidos' => 'Rest. Pedidos',
    'restaurante_menu' => 'Rest. Menú',
    'restaurante_pedidos_detalle' => 'Rest. Detalle Pedidos'
];

$section = isset($_GET['section']) ? $_GET['section'] : 'propiedades';
if (!array_key_exists($section, $secciones)) {
    $section = 'propiedades';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <!-- ... resto del header igual ... -->
    </header>
    <script>
        // ... tu script del header (invertir colores, etc.) ...
    </script>

    <main>
        <nav>
            <div class="enlaces">
                <?php foreach($secciones as $sec => $nombreSec): ?>
                    <div class="<?php echo ($sec == $section) ? 'activo':''; ?>">
                        <span class="icono relieve">
                            <?php echo mb_substr($nombreSec, 0, 1); ?>
                        </span>
                        <a href="?section=<?php echo $sec; ?>" style="color:inherit; text-decoration:none;">
                            <?php echo $nombreSec; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="operaciones">
                <div id="ocultar"><span class="icono relieve">></span>Ocultar</div>
            </div>
        </nav>
        <script>
            // ... tu script para mostrar/ocultar menú ...
        </script>

        <section>
            <?php
            switch($section) {
                case 'propiedades':
                    include "crud_propiedades.php";
                    break;
                case 'tipos_habitacion':
                    include "crud_tipos_habitacion.php";
                    break;
                case 'habitaciones':
                    include "crud_habitaciones.php";
                    break;
                case 'huespedes':
                    include "crud_huespedes.php";
                    break;
                case 'reservas':
                    include "crud_reservas.php";
                    break;
                case 'tarifas':
                    include "crud_tarifas.php";
                    break;
                case 'facturas':
                    include "crud_facturas.php";
                    break;
                case 'facturas_detalle':
                    include "crud_facturas_detalle.php";
                    break;
                case 'inventario':
                    include "crud_inventario.php";
                    break;
                case 'personal':
                    include "crud_personal.php";
                    break;
                case 'eventos':
                    include "crud_eventos.php";
                    break;
                case 'presupuestos':
                    include "crud_presupuestos.php";
                    break;
                case 'mantenimiento':
                    include "crud_mantenimiento.php";
                    break;
                case 'limpieza':
                    include "crud_limpieza.php";
                    break;
                case 'canales_venta':
                    include "crud_canales_venta.php";
                    break;
                case 'reservas_canales':
                    include "crud_reservas_canales.php";
                    break;
                case 'restaurante_mesas':
                    include "crud_restaurante_mesas.php";
                    break;
                case 'restaurante_pedidos':
                    include "crud_restaurante_pedidos.php";
                    break;
                case 'restaurante_menu':
                    include "crud_restaurante_menu.php";
                    break;
                case 'restaurante_pedidos_detalle':
                    include "crud_restaurante_pedidos_detalle.php";
                    break;
                default:
                    echo "<h3>Sección no implementada aún.</h3>";
                    break;
            }
            ?>
        </section>
    </main>
    <footer>
        <p>(c) 2025 jocarsa | aplicación</p>
    </footer>
</body>
</html>

