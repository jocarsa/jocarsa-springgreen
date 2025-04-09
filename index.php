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
        <title>jocarsa | aplicación</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <!-- Cabecera -->
        <header>
            <h1>
                <img src="https://static.jocarsa.com/logos/teal.png" alt="Logo">
                jocarsa | aplicación
            </h1>
            <nav>
                <!-- Conserva los botones de la cabecera tal como estaban -->
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
                    // Query for list of tables
                    $resultTables = $mysqli->query("SHOW TABLES");
                    if ($resultTables) {
                        while ($row = $resultTables->fetch_row()) {
                            // $row[0] contains the table name.
                            $tableName = $row[0];
                            echo "<div>";
                            echo "<a href=\"index.php?table=" . urlencode($tableName) . "&accion=listar\" style=\"color:white; text-decoration:none;\">";
                            echo htmlspecialchars($tableName);
                            echo "</a>";
                            echo "</div>";
                        }
                    }
                    ?>
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
                // If a table parameter is provided, include the dynamic super-controller.
                if(isset($_GET['table'])) {
                    include "super_controller.php";
                } else {
                    // Otherwise, show a welcome message.
                    echo "<h2>Bienvenido al Panel de Administración</h2>";
                    echo "<p>Seleccione una tabla del menú izquierdo para ver o editar sus registros.</p>";
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

