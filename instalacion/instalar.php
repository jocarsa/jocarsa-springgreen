<?php
// 1. Datos de conexión (puedes recibirlos por POST si tienes un formulario):
$host = 'localhost';
$user = 'springgreen';
$pass = 'springgreen';
$dbName = 'springgreen';

// 2. Crear la conexión
$conn = new mysqli($host, $user, $pass);

// Verificar error de conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 3. (Opcional) Crear la base de datos si no existe
$sqlCreateDB = "CREATE DATABASE IF NOT EXISTS `$dbName` 
                DEFAULT CHARACTER SET utf8mb4 
                COLLATE utf8mb4_unicode_ci;";
if (!$conn->query($sqlCreateDB)) {
    die("No se pudo crear la base de datos: " . $conn->error);
}

// 4. Seleccionar la base de datos
$conn->select_db($dbName);

// 5. Cargar el contenido del archivo .sql
//    (Asegúrate de usar la ruta correcta a tu archivo)
$sqlFilePath = __DIR__ . '/modelodedatos.sql';
$sqlScript = file_get_contents($sqlFilePath);

// Verificar que el archivo se haya leído correctamente
if ($sqlScript === false) {
    die("No se pudo leer el archivo SQL en la ruta especificada.");
}

// 6. Ejecutar todas las sentencias contenidas en el archivo .sql
if ($conn->multi_query($sqlScript)) {
    // multi_query ejecuta múltiples sentencias a la vez. 
    // Debemos iterar para "consumir" todos los resultados.
    do {
        // El siguiente llamado avanza al siguiente resultado (si existe)
    } while ($conn->more_results() && $conn->next_result());
    
    echo "Instalación completada. Las tablas se han creado correctamente en la base de datos '$dbName'.";
} else {
    echo "Error al crear las tablas: " . $conn->error;
}

// 7. Cerrar la conexión
$conn->close();
?>

