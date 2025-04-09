<?php
session_start();

// Si se envía el formulario de configuración, guardar los datos en sesión.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_config'])) {
    $_SESSION['server']   = trim($_POST['server']);
    $_SESSION['database'] = trim($_POST['database']);
    $_SESSION['user']     = trim($_POST['user']);
    $_SESSION['password'] = $_POST['password']; // Puede incluir espacios
    $mensaje = "Configuración guardada correctamente.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Instalación del Sistema</title>
  <style>
    /* Awesome CSS para la página de instalación */
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      padding: 0;
      font-family: 'Helvetica', sans-serif;
      background: linear-gradient(135deg, #3a6186, #89253e);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .contenedor {
      background: rgba(0, 0, 0, 0.6);
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
      max-width: 600px;
      width: 90%;
    }
    h1, h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    form {
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    input[type="text"],
    input[type="password"],
    input[type="date"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: none;
      border-radius: 5px;
    }
    button, .boton {
      background: #ff7e67;
      border: none;
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1em;
      margin: 5px;
      transition: background 0.3s;
      text-decoration: none;
      display: inline-block;
    }
    button:hover, .boton:hover {
      background: #ff3b2e;
    }
    .opciones {
      text-align: center;
    }
    .mensaje {
      background: #28a745;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 15px;
    }
    .error {
      background: #dc3545;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="contenedor">
    <h1>Instalación del Sistema</h1>
    
    <?php
      // Mostrar mensaje si existe
      if (isset($mensaje)) {
          echo "<div class='mensaje'>{$mensaje}</div>";
      }
    ?>

    <?php if (!isset($_SESSION['server']) || !isset($_SESSION['database']) || !isset($_SESSION['user'])): ?>
      <!-- Formulario para ingresar datos de conexión -->
      <form method="POST" action="">
        <label for="server">Servidor:</label>
        <input type="text" id="server" name="server" placeholder="Ej: localhost" required>
        
        <label for="database">Base de Datos:</label>
        <input type="text" id="database" name="database" placeholder="Nombre de la base de datos" required>
        
        <label for="user">Usuario:</label>
        <input type="text" id="user" name="user" placeholder="Usuario de la base de datos" required>
        
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required>
        
        <button type="submit" name="guardar_config">Guardar Configuración</button>
      </form>
    <?php else: ?>
      <!-- Menú de opciones de instalación -->
      <div class="opciones">
        <h2>Opciones de Instalación</h2>
        <p>Conectado a: <strong><?php echo htmlspecialchars($_SESSION['server']); ?></strong> | Base de Datos: <strong><?php echo htmlspecialchars($_SESSION['database']); ?></strong></p>
        <div>
          <!-- La opción "Eliminar Tablas" desactiva claves foráneas para forzar el borrado -->
          <a class="boton" href="?action=drop" onclick="return confirm('¿Está seguro de eliminar TODAS las tablas?')">Eliminar Tablas</a>
          <a class="boton" href="?action=truncate" onclick="return confirm('¿Está seguro de vaciar TODAS las tablas?')">Vaciar Tablas</a>
          <a class="boton" href="?action=instalar">Crear Tablas</a>
          <a class="boton" href="?action=datosdemuestra">Insertar Datos de Muestra</a>
          <a class="boton" href="?action=ir_app">Ir a la Aplicación</a>
        </div>
        <div style="margin-top:20px;">
          <a class="boton" href="?action=reset_config" onclick="return confirm('¿Desea reiniciar la configuración?')">Reiniciar Configuración</a>
        </div>
      </div>
    <?php endif; ?>

    <?php
    // Procesar acciones solicitadas mediante GET
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        // Verificar que la configuración esté disponible
        if (!isset($_SESSION['server']) || !isset($_SESSION['database']) || !isset($_SESSION['user'])) {
            echo "<div class='error'>La configuración de conexión no está establecida.</div>";
        } else {
            $server   = $_SESSION['server'];
            $database = $_SESSION['database'];
            $user     = $_SESSION['user'];
            $password = $_SESSION['password'];
            $mysqli   = new mysqli($server, $user, $password, $database);

            if ($mysqli->connect_error) {
                echo "<div class='error'>Error de conexión: " . $mysqli->connect_error . "</div>";
            } else {
                if ($action === 'drop') {
                    // Desactivar comprobación de claves foráneas y eliminar todas las tablas
                    $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");
                    $result = $mysqli->query("SHOW TABLES");
                    if ($result) {
                        $tables = [];
                        while ($row = $result->fetch_array()) {
                            $tables[] = $row[0];
                        }
                        foreach ($tables as $table) {
                            $mysqli->query("DROP TABLE IF EXISTS `$table`");
                        }
                        $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
                        echo "<div class='mensaje'>Se han eliminado todas las tablas de la base de datos '$database'.</div>";
                    } else {
                        echo "<div class='error'>Error al obtener las tablas: " . $mysqli->error . "</div>";
                    }
                } elseif ($action === 'truncate') {
                    // Desactivar comprobación de claves foráneas y truncar todas las tablas
                    $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");
                    $result = $mysqli->query("SHOW TABLES");
                    if ($result) {
                        $tables = [];
                        while ($row = $result->fetch_array()) {
                            $tables[] = $row[0];
                        }
                        foreach ($tables as $table) {
                            $mysqli->query("TRUNCATE TABLE `$table`");
                        }
                        $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
                        echo "<div class='mensaje'>Se han vaciado todas las tablas de la base de datos '$database'.</div>";
                    } else {
                        echo "<div class='error'>Error al obtener las tablas: " . $mysqli->error . "</div>";
                    }
                } elseif ($action === 'instalar') {
                    // Crear la base de datos (si no existe) y generar las tablas usando modelodedatos.sql
                    $sqlCreateDB = "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
                    if (!$mysqli->query($sqlCreateDB)) {
                        echo "<div class='error'>Error al crear la base de datos: " . $mysqli->error . "</div>";
                    }
                    $mysqli->select_db($database);
                    $sqlFilePath = __DIR__ . '/modelodedatos.sql';
                    $sqlScript   = file_get_contents($sqlFilePath);
                    if ($sqlScript === false) {
                        echo "<div class='error'>No se pudo leer el archivo modelodedatos.sql.</div>";
                    } else {
                        if ($mysqli->multi_query($sqlScript)) {
                            do {
                                // Consumir todos los resultados.
                            } while ($mysqli->more_results() && $mysqli->next_result());
                            echo "<div class='mensaje'>Instalación completada: Las tablas se han creado correctamente.</div>";
                        } else {
                            echo "<div class='error'>Error al crear las tablas: " . $mysqli->error . "</div>";
                        }
                    }
                } elseif ($action === 'datosdemuestra') {
                    // Insertar datos de muestra usando datosdemuestra.sql
                    $mysqli->select_db($database);
                    $sqlFilePath = __DIR__ . '/datosdemuestra.sql';
                    $sqlScript   = file_get_contents($sqlFilePath);
                    if ($sqlScript === false) {
                        echo "<div class='error'>No se pudo leer el archivo datosdemuestra.sql.</div>";
                    } else {
                        if ($mysqli->multi_query($sqlScript)) {
                            do {
                                // Consumir todos los resultados.
                            } while ($mysqli->more_results() && $mysqli->next_result());
                            echo "<div class='mensaje'>Los datos de ejemplo se han insertado correctamente en la base de datos '$database'.</div>";
                        } else {
                            echo "<div class='error'>Error al insertar datos de ejemplo: " . $mysqli->error . "</div>";
                        }
                    }
                } elseif ($action === 'reset_config') {
                    session_unset();
                    session_destroy();
                    echo "<div class='mensaje'>Configuración reiniciada. Recargue la página para reingresar los datos.</div>";
                } elseif ($action === 'ir_app') {
                    // Redirige a la aplicación principal (index.php en la raíz)
                    header("Location: ../index.php");
                    exit;
                }
                $mysqli->close();
            }
        }
    }
    ?>
  </div>
</body>
</html>

