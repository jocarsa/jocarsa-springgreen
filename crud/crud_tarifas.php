<?php
// crud_tarifas.php

$accion = $_GET['accion'] ?? 'listar';
$tarifa_id = $_GET['id'] ?? 0;

// CREAR / EDITAR
if ($_POST) {
    $tipo_habitacion_id = $_POST['tipo_habitacion_id'] ?? 0;
    $temporada = $_POST['temporada'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $moneda = $_POST['moneda'] ?? 'USD';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO tarifas (tipo_habitacion_id, temporada, precio, moneda) 
                                  VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $tipo_habitacion_id, $temporada, $precio, $moneda);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=tarifas");
        exit;
    } elseif ($accion == 'editar' && $tarifa_id > 0) {
        $stmt = $mysqli->prepare("UPDATE tarifas
                                  SET tipo_habitacion_id=?, temporada=?, precio=?, moneda=?
                                  WHERE tarifa_id=?");
        $stmt->bind_param("isdsi", $tipo_habitacion_id, $temporada, $precio, $moneda, $tarifa_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=tarifas");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $tarifa_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM tarifas WHERE tarifa_id=?");
    $stmt->bind_param("i", $tarifa_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=tarifas");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $tipos = $mysqli->query("SELECT tipo_habitacion_id, nombre_tipo FROM tipos_habitacion");
    ?>
    <h3>Crear Tarifa</h3>
    <form method="post" action="?section=tarifas&accion=crear">
        <label>Tipo Habitación:
            <select name="tipo_habitacion_id">
                <?php while($t = $tipos->fetch_assoc()): ?>
                    <option value="<?php echo $t['tipo_habitacion_id']; ?>">
                        <?php echo htmlspecialchars($t['nombre_tipo']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Temporada:
            <input type="text" name="temporada">
        </label><br><br>

        <label>Precio:
            <input type="number" step="0.01" name="precio">
        </label><br><br>

        <label>Moneda:
            <input type="text" name="moneda" value="USD">
        </label><br><br>

        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $tarifa_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM tarifas WHERE tarifa_id=?");
    $stmt->bind_param("i", $tarifa_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $tarifa = $res->fetch_assoc();
    $stmt->close();

    if (!$tarifa) {
        echo "<p>Tarifa no encontrada</p>";
        return;
    }

    $tipos = $mysqli->query("SELECT tipo_habitacion_id, nombre_tipo FROM tipos_habitacion");
    ?>
    <h3>Editar Tarifa</h3>
    <form method="post" action="?section=tarifas&accion=editar&id=<?php echo $tarifa_id; ?>">
        <label>Tipo Habitación:
            <select name="tipo_habitacion_id">
                <?php while($t = $tipos->fetch_assoc()): ?>
                    <option value="<?php echo $t['tipo_habitacion_id']; ?>"
                        <?php if($t['tipo_habitacion_id'] == $tarifa['tipo_habitacion_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($t['nombre_tipo']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Temporada:
            <input type="text" name="temporada" value="<?php echo htmlspecialchars($tarifa['temporada']); ?>">
        </label><br><br>

        <label>Precio:
            <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($tarifa['precio']); ?>">
        </label><br><br>

        <label>Moneda:
            <input type="text" name="moneda" value="<?php echo htmlspecialchars($tarifa['moneda']); ?>">
        </label><br><br>

        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT t.*, th.nombre_tipo
                          FROM tarifas t
                          JOIN tipos_habitacion th ON t.tipo_habitacion_id = th.tipo_habitacion_id
                          ORDER BY tarifa_id DESC");
?>
<h3>Tarifas</h3>
<p><a href="?section=tarifas&accion=crear" class="actualizar">+ Crear Nueva Tarifa</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tipo Habitación</th>
            <th>Temporada</th>
            <th>Precio</th>
            <th>Moneda</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['tarifa_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_tipo']); ?></td>
            <td><?php echo htmlspecialchars($fila['temporada']); ?></td>
            <td><?php echo htmlspecialchars($fila['precio']); ?></td>
            <td><?php echo htmlspecialchars($fila['moneda']); ?></td>
            <td>
                <a href="?section=tarifas&accion=editar&id=<?php echo $fila['tarifa_id']; ?>" class="actualizar">✏</a>
                <a href="?section=tarifas&accion=eliminar&id=<?php echo $fila['tarifa_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar esta tarifa?');">❌</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

