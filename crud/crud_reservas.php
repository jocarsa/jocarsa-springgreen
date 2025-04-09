<?php
// crud_reservas.php

$accion = $_GET['accion'] ?? 'listar';
$reserva_id = $_GET['id'] ?? 0;

// CREAR/EDITAR
if ($_POST) {
    $huesped_id = $_POST['huesped_id'] ?? 0;
    $propiedad_id = $_POST['propiedad_id'] ?? 0;
    $checkin = $_POST['fecha_checkin'] ?? '';
    $checkout = $_POST['fecha_checkout'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO reservas (huesped_id, propiedad_id, fecha_checkin, fecha_checkout, estado)
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $huesped_id, $propiedad_id, $checkin, $checkout, $estado);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=reservas");
        exit;
    } elseif ($accion == 'editar' && $reserva_id > 0) {
        $stmt = $mysqli->prepare("UPDATE reservas
            SET huesped_id=?, propiedad_id=?, fecha_checkin=?, fecha_checkout=?, estado=?
            WHERE reserva_id=?");
        $stmt->bind_param("iisssi", $huesped_id, $propiedad_id, $checkin, $checkout, $estado, $reserva_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=reservas");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $reserva_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM reservas WHERE reserva_id=?");
    $stmt->bind_param("i", $reserva_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=reservas");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    // Listas para selects
    $huespedes = $mysqli->query("SELECT huesped_id, nombre FROM huespedes");
    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Crear Reserva</h3>
    <form method="post" action="?section=reservas&accion=crear">
        <label>Huésped:
            <select name="huesped_id">
                <?php while($h = $huespedes->fetch_assoc()): ?>
                    <option value="<?php echo $h['huesped_id']; ?>"><?php echo htmlspecialchars($h['nombre']); ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"><?php echo htmlspecialchars($p['nombre_propiedad']); ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Fecha Check-in:
            <input type="date" name="fecha_checkin" required>
        </label><br><br>
        <label>Fecha Check-out:
            <input type="date" name="fecha_checkout" required>
        </label><br><br>
        <label>Estado:
            <input type="text" name="estado" value="pendiente">
        </label><br><br>

        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $reserva_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM reservas WHERE reserva_id=?");
    $stmt->bind_param("i", $reserva_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $reserva = $res->fetch_assoc();
    $stmt->close();

    if (!$reserva) {
        echo "<p>Reserva no encontrada</p>";
        return;
    }

    // Listas para selects
    $huespedes = $mysqli->query("SELECT huesped_id, nombre FROM huespedes");
    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Editar Reserva</h3>
    <form method="post" action="?section=reservas&accion=editar&id=<?php echo $reserva_id; ?>">
        <label>Huésped:
            <select name="huesped_id">
                <?php while($h = $huespedes->fetch_assoc()): ?>
                    <option value="<?php echo $h['huesped_id']; ?>"
                        <?php if($h['huesped_id'] == $reserva['huesped_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($h['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"
                        <?php if($p['propiedad_id'] == $reserva['propiedad_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Fecha Check-in:
            <input type="date" name="fecha_checkin" value="<?php echo htmlspecialchars($reserva['fecha_checkin']); ?>" required>
        </label><br><br>
        <label>Fecha Check-out:
            <input type="date" name="fecha_checkout" value="<?php echo htmlspecialchars($reserva['fecha_checkout']); ?>" required>
        </label><br><br>
        <label>Estado:
            <input type="text" name="estado" value="<?php echo htmlspecialchars($reserva['estado']); ?>">
        </label><br><br>

        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT r.*, h.nombre AS nombre_huesped, p.nombre_propiedad
                          FROM reservas r
                          JOIN huespedes h ON r.huesped_id = h.huesped_id
                          JOIN propiedades p ON r.propiedad_id = p.propiedad_id
                          ORDER BY r.reserva_id DESC");
?>
<h3>Reservas</h3>
<p><a href="?section=reservas&accion=crear" class="actualizar">+ Crear Nueva Reserva</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Huésped</th>
            <th>Propiedad</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['reserva_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_huesped']); ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
            <td><?php echo htmlspecialchars($fila['fecha_checkin']); ?></td>
            <td><?php echo htmlspecialchars($fila['fecha_checkout']); ?></td>
            <td><?php echo htmlspecialchars($fila['estado']); ?></td>
            <td>
                <a href="?section=reservas&accion=editar&id=<?php echo $fila['reserva_id']; ?>" class="actualizar">✏</a>
                <a href="?section=reservas&accion=eliminar&id=<?php echo $fila['reserva_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar esta reserva?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

