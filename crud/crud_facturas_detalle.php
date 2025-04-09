<?php
// crud_facturas_detalle.php

$accion = $_GET['accion'] ?? 'listar';
$detalle_id = $_GET['id'] ?? 0;

// CREAR / EDITAR
if ($_POST) {
    $factura_id = $_POST['factura_id'] ?? 0;
    $descripcion = $_POST['descripcion'] ?? '';
    $monto = $_POST['monto'] ?? 0.00;

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO facturas_detalle (factura_id, descripcion, monto)
                                  VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $factura_id, $descripcion, $monto);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=facturas_detalle");
        exit;
    } elseif ($accion == 'editar' && $detalle_id > 0) {
        $stmt = $mysqli->prepare("UPDATE facturas_detalle
                                  SET factura_id=?, descripcion=?, monto=?
                                  WHERE factura_detalle_id=?");
        $stmt->bind_param("isdi", $factura_id, $descripcion, $monto, $detalle_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=facturas_detalle");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $detalle_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM facturas_detalle WHERE factura_detalle_id=?");
    $stmt->bind_param("i", $detalle_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=facturas_detalle");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $facturas = $mysqli->query("SELECT factura_id FROM facturas");
    ?>
    <h3>Crear Detalle de Factura</h3>
    <form method="post" action="?section=facturas_detalle&accion=crear">
        <label>Factura:
            <select name="factura_id">
                <?php while($f = $facturas->fetch_assoc()): ?>
                    <option value="<?php echo $f['factura_id']; ?>"><?php echo $f['factura_id']; ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Descripción:
            <input type="text" name="descripcion">
        </label><br><br>
        <label>Monto:
            <input type="number" step="0.01" name="monto" value="0.00">
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $detalle_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM facturas_detalle WHERE factura_detalle_id=?");
    $stmt->bind_param("i", $detalle_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $detalle = $res->fetch_assoc();
    $stmt->close();

    if (!$detalle) {
        echo "<p>Detalle no encontrado</p>";
        return;
    }

    $facturas = $mysqli->query("SELECT factura_id FROM facturas");
    ?>
    <h3>Editar Detalle de Factura</h3>
    <form method="post" action="?section=facturas_detalle&accion=editar&id=<?php echo $detalle_id; ?>">
        <label>Factura:
            <select name="factura_id">
                <?php while($f = $facturas->fetch_assoc()): ?>
                    <option value="<?php echo $f['factura_id']; ?>"
                        <?php if($f['factura_id'] == $detalle['factura_id']) echo 'selected'; ?>>
                        <?php echo $f['factura_id']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Descripción:
            <input type="text" name="descripcion" value="<?php echo htmlspecialchars($detalle['descripcion']); ?>">
        </label><br><br>
        <label>Monto:
            <input type="number" step="0.01" name="monto" value="<?php echo htmlspecialchars($detalle['monto']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT fd.*, f.reserva_id 
                          FROM facturas_detalle fd
                          JOIN facturas f ON fd.factura_id = f.factura_id
                          ORDER BY factura_detalle_id DESC");
?>
<h3>Detalles de Facturas</h3>
<p><a href="?section=facturas_detalle&accion=crear" class="actualizar">+ Crear Nuevo Detalle</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Factura</th>
            <th>Reserva (referencia)</th>
            <th>Descripción</th>
            <th>Monto</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['factura_detalle_id']; ?></td>
            <td><?php echo $fila['factura_id']; ?></td>
            <td><?php echo $fila['reserva_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
            <td><?php echo $fila['monto']; ?></td>
            <td>
                <a href="?section=facturas_detalle&accion=editar&id=<?php echo $fila['factura_detalle_id']; ?>" class="actualizar">✏</a>
                <a href="?section=facturas_detalle&accion=eliminar&id=<?php echo $fila['factura_detalle_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este detalle?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

