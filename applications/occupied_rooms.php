<?php
// applications/occupied_rooms.php
// Reporte de Ocupación de Habitaciones utilizando el nuevo modelo de datos.

include "config.php";

$mysqli = new mysqli($host, $user, $pass, $dbName);
if ($mysqli->connect_errno) {
    die("Connection Error: " . $mysqli->connect_error);
}

// Establece el rango de fechas por defecto si no se envían desde el formulario.
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$end_date   = isset($_POST['end_date'])   ? $_POST['end_date']   : date('Y-m-t');

// Obtener todas las habitaciones (tabla "habitaciones")
$habitaciones = [];
$resultHab = $mysqli->query("SELECT * FROM habitaciones ORDER BY numero ASC");
while ($hab = $resultHab->fetch_assoc()) {
    $habitaciones[] = $hab;
}

// Obtener las reservas que se superponen con el período seleccionado
// Usamos las columnas 'fecha_checkin' y 'fecha_checkout' de la tabla "reservas".
$reservas = [];
$sql = "SELECT * FROM reservas 
        WHERE fecha_checkin <= '$end_date' AND fecha_checkout >= '$start_date'";
$resResult = $mysqli->query($sql);
while ($res = $resResult->fetch_assoc()) {
    $reservas[] = $res;
}

// Construir un arreglo de ocupación: $occupancy[habitacion_id][fecha] = true si está ocupada.
$occupancy = [];
foreach ($reservas as $res) {
    $habitacion_id = $res['habitacion_id'];
    $resStart = new DateTime($res['fecha_checkin']);
    $resEnd   = new DateTime($res['fecha_checkout']);
    // Se asume que el día del check-out no está ocupado.
    foreach (new DatePeriod($resStart, new DateInterval('P1D'), $resEnd) as $date) {
        $dateStr = $date->format('Y-m-d');
        $occupancy[$habitacion_id][$dateStr] = true;
    }
}

// Crear el período de fechas para la grilla.
$datePeriod = new DatePeriod(
    new DateTime($start_date),
    new DateInterval('P1D'),
    (new DateTime($end_date))->modify('+1 day')
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ocupación de Habitaciones</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: center;
        }
        .occupied {
            background-color: red;
            color: white;
        }
        .available {
            background-color: green;
            color: white;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Reporte de Ocupación de Habitaciones</h2>
    <form method="POST" action="?app=occupied_rooms">
        <label for="start_date">Fecha inicio:
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        </label>
        <label for="end_date">Fecha fin:
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        </label>
        <button type="submit">Generar Reporte</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <?php foreach ($habitaciones as $hab): ?>
                    <th><?php echo htmlspecialchars($hab['numero']); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datePeriod as $dateObj): 
                    $dateStr = $dateObj->format('Y-m-d');
            ?>
            <tr>
                <td><?php echo $dateStr; ?></td>
                <?php foreach ($habitaciones as $hab):
                        $ocupado = isset($occupancy[$hab['habitacion_id']][$dateStr]);
                ?>
                    <td class="<?php echo $ocupado ? 'occupied' : 'available'; ?>">
                        <?php echo $ocupado ? 'Ocupado' : 'Libre'; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
<?php
$mysqli->close();
?>

