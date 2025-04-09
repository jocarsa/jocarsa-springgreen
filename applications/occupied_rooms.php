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

// Construir un arreglo de ocupación por habitación y fecha.
// En lugar de un simple true/false, almacenamos aquí el identificador de la reserva
// (usamos $res['id'] si está definido; de lo contrario se crea un identificador único combinando la fecha de check-in y el id del huésped).
$occupancy = [];
foreach ($reservas as $res) {
    $habitacion_id = $res['habitacion_id'];
    $resId = isset($res['id']) ? $res['id'] : $res['fecha_checkin'] . '_' . $res['huesped_id'];
    $resStart = new DateTime($res['fecha_checkin']);
    $resEnd   = new DateTime($res['fecha_checkout']);
    // Se asume que el día del check-out no está ocupado.
    foreach (new DatePeriod($resStart, new DateInterval('P1D'), $resEnd) as $date) {
        $dateStr = $date->format('Y-m-d');
        $occupancy[$habitacion_id][$dateStr] = $resId;
    }
}

// Crear el período de fechas para la grilla.
$datePeriod = new DatePeriod(
    new DateTime($start_date),
    new DateInterval('P1D'),
    (new DateTime($end_date))->modify('+1 day')
);

// Convertir el DatePeriod a un arreglo de fechas en formato "Y-m-d"
// para facilitar el uso de índices cuando se evalúan las celdas vecinas.
$dates = [];
foreach ($datePeriod as $dateObj) {
    $dates[] = $dateObj->format('Y-m-d');
}
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
            /* By default, all cells have a 1px border */
        }
        /* Occupied cells will use a red background.
           The additional inline styles (added from PHP) will show thick borders to group reservation days. */
        .occupied {
            background-color: rgb(255,200,200);
            border-left: 3px solid black;
            border-right: 3px solid black;
        }
        /* Available cells use a green background. */
        .available {
            background-color: rgb(200,255,200);
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
            <?php
            // Usamos un bucle for para poder conocer el índice (y así identificar celdas vecinas)
            $totalDays = count($dates);
            for ($i = 0; $i < $totalDays; $i++):
                $dateStr = $dates[$i];
            ?>
            <tr>
                <td><?php echo $dateStr; ?></td>
                <?php foreach ($habitaciones as $hab):
                    // Para cada celda, comprobar si hay una reserva.
                    $roomId = $hab['habitacion_id'];
                    $currentRes = isset($occupancy[$roomId][$dateStr]) ? $occupancy[$roomId][$dateStr] : null;
                ?>
                    <?php if ($currentRes): 
                        // La celda está ocupada. Ahora, agregar “bordes agrupadores” para marcar el inicio y fin
                        $style = "";
                        // Verificar el día anterior
                        if ($i === 0) {
                            $style .= "border-top: 3px solid black;";
                        } else {
                            $prevDate = $dates[$i - 1];
                            $prevRes = isset($occupancy[$roomId][$prevDate]) ? $occupancy[$roomId][$prevDate] : null;
                            if ($prevRes !== $currentRes) {
                                $style .= "border-top: 3px solid black;";
                            }
                        }
                        // Verificar el día siguiente
                        if ($i === $totalDays - 1) {
                            $style .= "border-bottom: 3px solid black;";
                        } else {
                            $nextDate = $dates[$i + 1];
                            $nextRes = isset($occupancy[$roomId][$nextDate]) ? $occupancy[$roomId][$nextDate] : null;
                            if ($nextRes !== $currentRes) {
                                $style .= "border-bottom: 3px solid black;";
                            }
                        }
                    ?>
                        <td class="occupied" style="<?php echo $style; ?>"></td>
                    <?php else: 
                        // La celda está disponible.
                        // En lugar de texto, se crea un enlace que lleva al formulario de reserva
                        // (ajusta el URL y parámetros según la lógica de tu aplicación).
                        $link = "http://localhost/jocarsa-springgreen/index.php?table=reservas&accion=crear&fecha_checkin=" . urlencode($dateStr) . "&habitacion_id=" . urlencode($roomId);
                    ?>
                        <td class="available">
                            <a href="<?php echo $link; ?>" style="display:block;box-sizing:border-box;opacity:0.1;width:100%;height:100%;text-decoration:none;">&nbsp;</a>
                        </td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</body>
</html>
<?php
$mysqli->close();
?>

