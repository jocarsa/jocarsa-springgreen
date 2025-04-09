<?php
// crud_canales_venta.php

$accion = $_GET['accion'] ?? 'listar';
$canal_id = $_GET['id'] ?? 0;

if ($_POST) {
    $nombre_canal = $_POST['nombre_canal'] ?? '';
    $comision = $_POST['comision'] ?? 0.00;

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO canales_venta (nombre_canal, commission_rate)
                                  VALUES (?, ?)");
        $stmt->bind_param("sd", $nombre_canal, $comision);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=canales_venta");
        exit;
    } elseif ($accion == 'editar' && $canal_id > 0) {
        $stmt = $mysqli->prepare("UPDATE canales_venta
                                  SET nombre_canal=?, commission_rate=?
                                  WHERE channel_id=?");
        $stmt->bind_param("sdi", $nombre_canal, $comision, $canal_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=canales_venta");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $canal_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM canales_venta WHERE channel_id=?");
    $stmt->bind_param("i", $canal_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=canales_venta");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    ?>
    <h3>Crear Canal de Venta</h3>
    <form method="post" action="?section=canales_venta&accion=crear">
        <label>Nombre Canal:
            <input type="text" name="nombre_canal" required>
        </label><br><br>
        <label>Comisión (%):
            <input type="number" step="0.01" name="comision" value="0.00">
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $canal_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM canales_venta WHERE channel_id=?");
    $stmt->bind_param("i", $canal_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $canal = $res->fetch_assoc();
    $stmt->close();

    if (!$canal) {
        echo "<p>Canal no encontrado</p>";
        return;
    }
    ?>
    <h3>Editar Canal de Venta</h3>
    <form method="post" action="?section=canales_venta&accion=editar&id=<?php echo $canal_id; ?>">
        <label>Nombre Canal:
            <input type="text" name="nombre_canal" value="<?php echo htmlspecialchars($canal['nombre_canal']); ?>" required>
        </label><br><br>
        <label>Comisión (%):
            <input type="number" step="0.01" name="comision" value="<?php echo htmlspecialchars($canal['commission_rate']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT * FROM canales_venta ORDER BY channel_id DESC");
?>
<h3>Canales de Venta</h3>
<p><a href="?section=canales_venta&accion=crear" class="actualizar">+ Crear Canal</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre Canal</th>
            <th>Comisión (%)</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['channel_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_canal']); ?></td>
            <td><?php echo $fila['commission_rate']; ?></td>
            <td>
                <a href="?section=canales_venta&accion=editar&id=<?php echo $fila['channel_id']; ?>" class="actualizar">✏</a>
                <a href="?section=canales_venta&accion=eliminar&id=<?php echo $fila['channel_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este canal?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

