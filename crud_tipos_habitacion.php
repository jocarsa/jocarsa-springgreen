<?php
// crud_tipos_habitacion.php

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';
$tipo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// CREAR / EDITAR (procesamiento del formulario)
if ($_POST) {
    $nombre_tipo = $_POST['nombre_tipo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO tipos_habitacion (nombre_tipo, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre_tipo, $descripcion);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=tipos_habitacion");
        exit;
    } elseif ($accion == 'editar' && $tipo_id > 0) {
        $stmt = $mysqli->prepare("UPDATE tipos_habitacion SET nombre_tipo=?, descripcion=? WHERE tipo_habitacion_id=?");
        $stmt->bind_param("ssi", $nombre_tipo, $descripcion, $tipo_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=tipos_habitacion");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $tipo_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM tipos_habitacion WHERE tipo_habitacion_id=?");
    $stmt->bind_param("i", $tipo_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=tipos_habitacion");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    ?>
    <h3>Crear Tipo de Habitación</h3>
    <form method="post" action="?section=tipos_habitacion&accion=crear">
        <label>Nombre Tipo:
            <input type="text" name="nombre_tipo" required>
        </label><br><br>
        <label>Descripción:
            <textarea name="descripcion"></textarea>
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $tipo_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM tipos_habitacion WHERE tipo_habitacion_id=?");
    $stmt->bind_param("i", $tipo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tipo = $result->fetch_assoc();
    $stmt->close();

    if (!$tipo) {
        echo "<p>No se encontró el tipo de habitación</p>";
        return;
    }
    ?>
    <h3>Editar Tipo de Habitación</h3>
    <form method="post" action="?section=tipos_habitacion&accion=editar&id=<?php echo $tipo_id; ?>">
        <label>Nombre Tipo:
            <input type="text" name="nombre_tipo" value="<?php echo htmlspecialchars($tipo['nombre_tipo']); ?>" required>
        </label><br><br>
        <label>Descripción:
            <textarea name="descripcion"><?php echo htmlspecialchars($tipo['descripcion']); ?></textarea>
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT * FROM tipos_habitacion ORDER BY tipo_habitacion_id DESC");
?>
<h3>Tipos de Habitación</h3>
<p><a href="?section=tipos_habitacion&accion=crear" class="actualizar">+ Crear Nuevo Tipo</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre Tipo</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($fila = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila['tipo_habitacion_id']; ?></td>
                <td><?php echo htmlspecialchars($fila['nombre_tipo']); ?></td>
                <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                <td>
                    <a href="?section=tipos_habitacion&accion=editar&id=<?php echo $fila['tipo_habitacion_id']; ?>" class="actualizar">✏</a>
                    <a href="?section=tipos_habitacion&accion=eliminar&id=<?php echo $fila['tipo_habitacion_id']; ?>" class="eliminar"
                       onclick="return confirm('¿Eliminar este tipo de habitación?');">❌</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

