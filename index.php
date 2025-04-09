<?php
session_start();
include "config.php";
// Create database connection.
$mysqli = new mysqli($host, $user, $pass, $dbName);
if ($mysqli->connect_errno) {
    die("MySQL Connection Error: " . $mysqli->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>jocarsa | springgreen</title>
        <link rel="stylesheet" href="styles.css">
        <style>
            
        </style>
        <link rel="icon" type="image/svg+xml" href="springgreen.png" />
    </head>
    <body>
        <!-- Cabecera -->
        <header>
            <h1>
            <a href="?">
                <img src="springgreen.png" alt="Logo">
                jocarsa | springgreen
                </a>
            </h1>
            <nav>
                <!-- Botones de la cabecera -->
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve">A</button>
                <button class="boton relieve" id="invertir"><span class="icono">☀</span></button>
                <button class="boton relieve" id="textogrande"><span class="icono">🔎</span></button>
                <button class="boton relieve" id="textopequeno"><span class="icono">🔎</span></button>
            </nav>
            <div id="cerrarsesion">🔒</div>
        </header>
        <!-- Fin Cabecera -->

        <main>
            <!-- Menú de navegación lateral (izquierdo) -->
            <nav>
                <div class="enlaces">
                    <?php
                    // Listar todas las tablas.
                    $resultTables = $mysqli->query("SHOW TABLES");
                    if ($resultTables) {
                        while ($row = $resultTables->fetch_row()) {
                            $tableName = $row[0];
                            echo "<div ";
                            if(isset($_GET['table']) && $tableName == $_GET['table']){
                                echo " class='activo' ";
                            }
                            echo ">";
                            echo '<span class="icono relieve">'.htmlspecialchars($tableName[0]).'</span>';
                            echo "<a href=\"index.php?table=" . urlencode($tableName) . "&accion=listar\" style=\"color:white; text-decoration:none;\">";
                            echo htmlspecialchars($tableName);
                            echo "</a>";
                            echo "</div>";
                        }
                    }
                    ?>
                </div>
                <!-- Applications Section -->
                <div class="applications" style="margin-top:20px;">
                    <h3 style="color:white; padding:10px 0;">Aplicaciones</h3>
                    <div class="enlaces">
                        <div>
                            <span class="icono relieve">A</span>
                            <!-- Link now passes app parameter -->
                            <a href="index.php?app=occupied_rooms" style="color:white; text-decoration:none;">
                                Ocupación de Habitaciones
                            </a>
                        </div>
                        <!-- Add more application links as needed -->
                    </div>
                </div>
                <div class="operaciones">
                    <div id="ocultar">
                        <span class="icono relieve">></span> Ocultar
                    </div>
                </div>
            </nav>
            <!-- Fin Menú de navegación lateral -->

            <!-- Sección principal -->
            <section>
                <?php
                // If an application parameter is provided, include the corresponding application.
                if(isset($_GET['app'])) {
                    $app = $_GET['app'];
                    switch ($app) {
                        case 'occupied_rooms':
                            include "applications/occupied_rooms.php";
                            break;
                        default:
                            echo "<h2>Aplicación no reconocida.</h2>";
                    }
                }
                // Otherwise, if a table parameter is provided, include the dynamic super-controller.
                elseif(isset($_GET['table'])) {
                    include "super_controller.php";
                }
                // Otherwise, show the dashboard grid.
                else {
                    echo '<div class="dashboard-grid">';
                    
                    // Dashboard section: Tables.
                    echo '<div class="dashboard-section">';
                    echo '<h2>Tablas</h2>';
                    if ($resultTables) {
                        // Reset pointer to ensure all rows are available.
                        $resultTables->data_seek(0);
                        while ($row = $resultTables->fetch_row()) {
                            $tableName = $row[0];
                            echo '<div class="card">';
                            echo '<a href="index.php?table=' . urlencode($tableName) . '&accion=listar"><span class="iconoletra">' . htmlspecialchars($tableName)[0] . '</span> ' . htmlspecialchars($tableName) . '</a>';
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                    
                    // Dashboard section: Applications.
                    echo '<div class="dashboard-section">';
                    echo '<h2>Aplicaciones</h2>';
                    // Define your applications in an array. Extend as needed.
                    $applications = [
                        ["app" => "occupied_rooms", "label" => "Ocupación de Habitaciones"]
                    ];
                    foreach ($applications as $app) {
                        echo '<div class="card">';
                        echo '<a href="index.php?app=' . urlencode($app["app"]) . '">' . htmlspecialchars($app["label"]) . '</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                    
                    echo '</div>';  // End dashboard-grid.
                }
                ?>
            </section>
        </main>
        <!-- Fin Sección principal -->

        <!-- Pie de Página -->
        <footer>
            <p>(c) 2025 jocarsa | aplicación</p>
        </footer>
    </body>
</html>

