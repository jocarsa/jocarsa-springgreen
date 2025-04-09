<?php
session_start();
include "config.php";

// Create MySQL connection.
$mysqli = new mysqli($host, $user, $pass, $dbName);
if ($mysqli->connect_errno) {
    die("MySQL Connection Error: " . $mysqli->connect_error);
}
// Consultar las tablas de la base de datos.
$result = $mysqli->query("SHOW TABLES");
if ($result && $result->num_rows == 0) {
    // Si no se encontraron tablas, redirige al instalador.
    header("Location: instalacion/index.php");
    exit;
}
// Process logout if requested.
if (isset($_GET['logout'])) {
    // Destroy session and redirect to login.
    $_SESSION = array();
    session_destroy();
    header("Location: index.php");
    exit;
}

// LOGIN CHECK: If the user is not logged in, then process or display the login form.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Process login form submission.
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario']) && isset($_POST['contrasena'])) {
        $usuario    = $mysqli->real_escape_string($_POST['usuario']);
        $contrasena = $mysqli->real_escape_string($_POST['contrasena']);

        // Query usuarios table for matching user.
        $query = "SELECT * FROM usuarios WHERE username = '$usuario' LIMIT 1";
        $result = $mysqli->query($query);
        if ($result && $result->num_rows == 1) {
            $userRow = $result->fetch_assoc();
            // In production, use password_verify() when storing hashed passwords.
            if ($userRow['password'] === $contrasena) {
                // Credentials are valid – set session variables and redirect.
                $_SESSION['loggedin'] = true;
                $_SESSION['user'] = $userRow;
                header("Location: index.php");
                exit;
            } else {
                $login_error = "Usuario o contraseña incorrectos.";
            }
        } else {
            $login_error = "Usuario o contraseña incorrectos.";
        }
    }
    // Display the login form if not logged in.
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Iniciar Sesión</title>
        <style>
            body, html {
                padding: 0;
                margin: 0;
                font-family: sans-serif;
                height: 100%;
            }
            body {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                background: rgb(240,240,240);
            }
            form {
                width: 200px;
                height: 400px;
                border: 1px solid lightgrey;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                border-radius: 5px;
                box-shadow: 0px 4px 8px rgba(0,0,0,0.3);
                background: white;
                padding: 30px;
                font-size: 11px;
            }
            form img {
                width: 80%;
                margin-bottom: 15px;
            }
            form input[type="text"],
            form input[type="password"],
            form input[type="submit"] {
                box-sizing: border-box;
                padding: 10px;
                border: 1px solid lightgrey;
                border-radius: 5px;
                width: 100%;
                margin: 10px 0;
            }
            form input[type="text"],
            form input[type="password"] {
                box-shadow: inset 0px 4px 8px rgba(0,0,0,0.3);
            }
            form input[type="submit"] {
                background: #2980b9;
                color: white;
                box-shadow:
                    0 1px #2980b9,
                    0 2px #2471a3,
                    0 3px #1f618d,
                    0 4px #1a5276,
                    0 5px #154360,
                    0 8px 10px rgba(0,0,0,0.6);
                cursor: pointer;
            }
            form div {
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
                margin: 10px 0;
            }
            .error {
                color: red;
                font-size: 10px;
                margin: 5px;
            }
        </style>
    </head>
    <body>
        <form method="post" action="index.php">
            <img src="springgreen.png" alt="Logo">
            <label>Usuario:</label>
            <input type="text" name="usuario" required>
            <label>Contraseña:</label>
            <input type="password" name="contrasena" required>
            <div>
                <input type="checkbox" name="recuerdame"> Recuérdame
            </div>
            <?php
            if (isset($login_error)) {
                echo '<div class="error">' . htmlspecialchars($login_error) . '</div>';
            }
            ?>
            <input type="submit" value="Iniciar Sesión">
        </form>
    </body>
    </html>
    <?php
    exit;
}

// If the user is logged in, continue to render the dashboard below.
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>jocarsa | springgreen</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="icon" type="image/svg+xml" href="springgreen.png" />
    </head>
    <body>
        <main>
            <!-- Menú de navegación lateral (izquierdo) -->
            <nav>
                <h1>
                    <a href="index.php">
                        <img src="springgreen.png" alt="Logo">
                        springgreen
                    </a>
                </h1>
                <div class="enlaces">
                    <?php
                    // Listar todas las tablas.
                    $resultTables = $mysqli->query("SHOW TABLES");
                    if ($resultTables) {
                        while ($row = $resultTables->fetch_row()) {
                            $tableName = $row[0];
                            echo "<div ";
                            if (isset($_GET['table']) && $tableName == $_GET['table']) {
                                echo " class='activo' ";
                            }
                            echo ">";
                            echo '<span class="icono relieve">' . htmlspecialchars($tableName[0]) . '</span>';
                            echo "<a href=\"index.php?table=" . urlencode($tableName) . "&accion=listar\" style=\"color:white; text-decoration:none;\">";
                            echo htmlspecialchars($tableName);
                            echo "</a>";
                            echo "</div>";
                        }
                    }
                    ?>
                    <!-- Logout action at the end of the left nav -->
                    <div class="logout">
                        <a href="index.php?logout=true" style="color:white; text-decoration:none; padding:10px;">
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
                <!-- Applications Section -->
                <div class="applications" style="margin-top:20px;">
                    <h3 style="color:white; padding:10px 0;">Aplicaciones</h3>
                    <div class="enlaces">
                        <div>
                            <span class="icono relieve">A</span>
                            <a href="index.php?app=occupied_rooms" style="color:white; text-decoration:none;">
                                Ocupación de Habitaciones
                            </a>
                        </div>
                    </div>
                </div>
                <div class="operaciones">
                    <div id="ocultar">
                        <span class="icono relieve">></span> Ocultar
                    </div>
                </div>
            </nav>

            <!-- Sección principal -->
            <section>
                <?php
                // If an application parameter is provided, include the corresponding application.
                if (isset($_GET['app'])) {
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
                elseif (isset($_GET['table'])) {
                    include "super_controller.php";
                }
                // Otherwise, show the dashboard grid.
                else {
                    echo '<div class="dashboard-grid">';
                    
                    // Dashboard section: Tables.
                    echo '<div class="dashboard-section">';
                    echo '<h2>Tablas</h2>';
                    if ($resultTables) {
                        // Reset pointer to list all tables.
                        $resultTables->data_seek(0);
                        while ($row = $resultTables->fetch_row()) {
                            $tableName = $row[0];
                            echo '<div class="card">';
                            echo '<a href="index.php?table=' . urlencode($tableName) . '&accion=listar">';
                            echo '<span class="iconoletra">' . htmlspecialchars($tableName)[0] . '</span> ' . htmlspecialchars($tableName);
                            echo '</a>';
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                    
                    // Dashboard section: Applications.
                    echo '<div class="dashboard-section">';
                    echo '<h2>Aplicaciones</h2>';
                    // Define your applications in an array.
                    $applications = [
                        ["app" => "occupied_rooms", "label" => "Ocupación de Habitaciones"]
                    ];
                    foreach ($applications as $app) {
                        echo '<div class="card">';
                        echo '<a href="index.php?app=' . urlencode($app["app"]) . '">';
                        echo htmlspecialchars($app["label"]);
                        echo '</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                    
                    echo '</div>';  // End dashboard-grid.
                }
                ?>
            </section>
        </main>
    </body>
</html>
<?php
// Close the database connection.
//$mysqli->close();
?>

