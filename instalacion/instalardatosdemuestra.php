<?php
/**
 * install_sample_data.php
 * 
 * Script para cargar datos de ejemplo en la base de datos del PMS.
 */

// 1. Capturar o definir los datos de conexión (en un entorno real, usar $_POST o una configuración)
$host = 'localhost';
$user = 'springgreen';
$pass = 'springgreen';
$dbName = 'springgreen'; // Asegúrate de usar el mismo nombre de la BD donde ya creaste las tablas

// 2. Crear la conexión
$conn = new mysqli($host, $user, $pass);

// Verificar error de conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 3. Seleccionar la base de datos donde ya están creadas las tablas
$conn->select_db($dbName);

// 4. Cargar el contenido del archivo sample_data.sql
//    Ajusta la ruta al lugar donde hayas guardado el archivo .sql
$sqlFilePath = __DIR__ . '/datosdemuestra.sql';
$sqlScript = file_get_contents($sqlFilePath);

if ($sqlScript === false) {
    die("No se pudo leer el archivo SQL (sample_data.sql). Verifica la ruta y permisos de lectura.");
}

// 5. Ejecutar las sentencias
if ($conn->multi_query($sqlScript)) {
    do {
        // Avanza en cada resultado, si existe
    } while ($conn->more_results() && $conn->next_result());
    echo "Los datos de ejemplo se han insertado correctamente en la base de datos '$dbName'.";
} else {
    echo "Error al insertar datos de ejemplo: " . $conn->error;
}

// 6. Cerrar conexión
$conn->close();
?>

