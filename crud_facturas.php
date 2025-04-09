<?php
// crud_facturas.php

$accion = $_GET['accion'] ?? 'listar';
$factura_id = $_GET['id'] ?? 0;

if ($_POST) {
    $reserva_id = $_POST['reserva_id'] ?? 0;
    $monto_total = $_POST['monto_total'] ?? 0.00;
    $estado = $_POST['estado'] ?? 'pendiente';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO facturas (reserva_id, monto_total, estado)
                                  VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $reserva_id, $monto_total, $estado);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=facturas");
        exit;
    } elseif ($accion == 'editar' && $factura_id > 0) {
        $stmt = $mysqli->prepare("UPDATE facturas
                                  SET reserva_id=?, monto_total=?, estado=?
                                  WHERE factura_id=?");
        $stmt->bind_param("idsi", $reserva_id, $monto_total, $estado, $factura_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=facturas");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $factura_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM facturas WHERE factura_id=?");
    $stmt->bind_param("i", $factura_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=facturas");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    // Listar reservas
    $reservas = $mysqli->query("SELECT reserva_id FROM reservas");
    ?>
    <h3>Crear Factura</h3>
    <form method="post" action="?section=facturas&accion=crear">
        <label>Reserva:
            <select name="reserva_id">
                <?php while($r = $reservas->fetch_assoc()): ?>
                    <option value="<?php echo $r['reserva_id']; ?>"><?php echo $r['reserva_id']; ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Monto Total:
            <input type="number" step="0.01" name="monto_total" value="0.00">
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
if ($accion == 'editar' && $factura_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM facturas WHERE factura_id=?");
    $stmt->bind_param("i", $factura_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $factura = $res->fetch_assoc();
    $stmt->close();

    if (!$factura) {
        echo "<p>Factura no encontrada</p>";
        return;
    }

    // Listar reservas
    $reservas = $mysqli->query("SELECT reserva_id FROM reservas");
    ?>
    <h3>Editar Factura</h3>
    <form method="post" action="?section=facturas&accion=editar&id=<?php echo $factura_id; ?>">
        <label>Reserva:
            <select name="reserva_id">
                <?php while($r = $reservas->fetch_assoc()): ?>
                    <option value="<?php echo $r['reserva_id']; ?>"
                        <?php if($r['reserva_id'] == $factura['reserva_id']) echo 'selected'; ?>>
                        <?php echo $r['reserva_id']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Monto Total:
            <input type="number" step="0.01" name="monto_total" value="<?php echo htmlspecialchars($factura['monto_total']); ?>">
        </label><br><br>
        <label>Estado:
            <input type="text" name="estado" value="<?php echo htmlspecialchars($factura['estado']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT f.*, r.reserva_id as reserva 
                          FROM facturas f
                          JOIN reservas r ON f.reserva_id = r.reserva_id
                          ORDER BY factura_id DESC");
?>
<h3>Facturas</h3>
<p><a href="?section=facturas&accion=crear" class="actualizar">+ Crear Nueva Factura</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Reserva</th>
            <th>Monto Total</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['factura_id']; ?></td>
            <td><?php echo $fila['reserva']; ?></td>
            <td><?php echo $fila['monto_total']; ?></td>
            <td><?php echo htmlspecialchars($fila['estado']); ?></td>
            <td>
                <a href="?section=facturas&accion=editar&id=<?php echo $fila['factura_id']; ?>" class="actualizar">✏</a>
                <a href="?section=facturas&accion=eliminar&id=<?php echo $fila['factura_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar esta factura?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

