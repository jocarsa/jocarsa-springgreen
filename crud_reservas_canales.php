<?php
// crud_reservas_canales.php

$accion = $_GET['accion'] ?? 'listar';
// No hay ID único, la PK es compuesta (reservation_id, channel_id).
// Para simplificar, tomamos 2 GETs: &res_id= y &ch_id=
$res_id = $_GET['res_id'] ?? 0;
$ch_id = $_GET['ch_id'] ?? 0;

// CREAR
if ($_POST && $accion == 'crear') {
    $reserva_id = $_POST['reserva_id'] ?? 0;
    $canal_id = $_POST['canal_id'] ?? 0;

    $stmt = $mysqli->prepare("INSERT INTO reservas_canales (reserva_id, channel_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $reserva_id, $canal_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=reservas_canales");
    exit;
}

// ELIMINAR
if ($accion == 'eliminar') {
    // Debemos tener res_id y ch_id
    if ($res_id && $ch_id) {
        $stmt = $mysqli->prepare("DELETE FROM reservas_canales WHERE reservation_id=? AND channel_id=?");
        $stmt->bind_param("ii", $res_id, $ch_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ?section=reservas_canales");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $reservas = $mysqli->query("SELECT reserva_id FROM reservas");
    $canales = $mysqli->query("SELECT channel_id, nombre_canal FROM canales_venta");
    ?>
    <h3>Agregar Canal a Reserva</h3>
    <form method="post" action="?section=reservas_canales&accion=crear">
        <label>Reserva:
            <select name="reserva_id">
                <?php while($r = $reservas->fetch_assoc()): ?>
                    <option value="<?php echo $r['reserva_id']; ?>"><?php echo $r['reserva_id']; ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Canal:
            <select name="canal_id">
                <?php while($c = $canales->fetch_assoc()): ?>
                    <option value="<?php echo $c['channel_id']; ?>"><?php echo htmlspecialchars($c['nombre_canal']); ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <button type="submit" class="actualizar">Agregar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT rc.reservation_id, rc.channel_id, r.estado as estado_reserva, c.nombre_canal
                          FROM reservas_canales rc
                          JOIN reservas r ON rc.reservation_id = r.reserva_id
                          JOIN canales_venta c ON rc.channel_id = c.channel_id
                          ORDER BY rc.reservation_id, rc.channel_id");
?>
<h3>Relación Reservas - Canales</h3>
<p><a href="?section=reservas_canales&accion=crear" class="actualizar">+ Agregar Canal a una Reserva</a></p>
<table>
    <thead>
        <tr>
            <th>ID Reserva</th>
            <th>Canal</th>
            <th>Estado Reserva</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['reservation_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_canal']); ?></td>
            <td><?php echo htmlspecialchars($fila['estado_reserva']); ?></td>
            <td>
                <a href="?section=reservas_canales&accion=eliminar&res_id=<?php echo $fila['reservation_id']; ?>&ch_id=<?php echo $fila['channel_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Quitar este canal de la reserva?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

