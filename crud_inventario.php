<?php
// crud_inventario.php

$accion = $_GET['accion'] ?? 'listar';
$inventario_id = $_GET['id'] ?? 0;

if ($_POST) {
    $propiedad_id = $_POST['propiedad_id'] ?? 0;
    $nombre_item = $_POST['nombre_item'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $ubicacion = $_POST['ubicacion'] ?? '';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO inventario (propiedad_id, nombre_item, cantidad, ubicacion)
                                  VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $propiedad_id, $nombre_item, $cantidad, $ubicacion);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=inventario");
        exit;
    } elseif ($accion == 'editar' && $inventario_id > 0) {
        $stmt = $mysqli->prepare("UPDATE inventario
                                  SET propiedad_id=?, nombre_item=?, cantidad=?, ubicacion=?
                                  WHERE inventario_id=?");
        $stmt->bind_param("isisi", $propiedad_id, $nombre_item, $cantidad, $ubicacion, $inventario_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=inventario");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $inventario_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM inventario WHERE inventario_id=?");
    $stmt->bind_param("i", $inventario_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=inventario");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Crear Item de Inventario</h3>
    <form method="post" action="?section=inventario&accion=crear">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>">
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Nombre Item:
            <input type="text" name="nombre_item" required>
        </label><br><br>
        <label>Cantidad:
            <input type="number" name="cantidad" value="0">
        </label><br><br>
        <label>Ubicación:
            <input type="text" name="ubicacion">
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $inventario_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM inventario WHERE inventario_id=?");
    $stmt->bind_param("i", $inventario_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $item = $res->fetch_assoc();
    $stmt->close();

    if (!$item) {
        echo "<p>Item no encontrado</p>";
        return;
    }

    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Editar Item de Inventario</h3>
    <form method="post" action="?section=inventario&accion=editar&id=<?php echo $inventario_id; ?>">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"
                        <?php if($p['propiedad_id'] == $item['propiedad_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Nombre Item:
            <input type="text" name="nombre_item" value="<?php echo htmlspecialchars($item['nombre_item']); ?>" required>
        </label><br><br>
        <label>Cantidad:
            <input type="number" name="cantidad" value="<?php echo htmlspecialchars($item['cantidad']); ?>">
        </label><br><br>
        <label>Ubicación:
            <input type="text" name="ubicacion" value="<?php echo htmlspecialchars($item['ubicacion']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT i.*, p.nombre_propiedad
                          FROM inventario i
                          JOIN propiedades p ON i.propiedad_id = p.propiedad_id
                          ORDER BY i.inventario_id DESC");
?>
<h3>Inventario</h3>
<p><a href="?section=inventario&accion=crear" class="actualizar">+ Crear Nuevo Item</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Propiedad</th>
            <th>Nombre Item</th>
            <th>Cantidad</th>
            <th>Ubicación</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['inventario_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_item']); ?></td>
            <td><?php echo $fila['cantidad']; ?></td>
            <td><?php echo htmlspecialchars($fila['ubicacion']); ?></td>
            <td>
                <a href="?section=inventario&accion=editar&id=<?php echo $fila['inventario_id']; ?>" class="actualizar">✏</a>
                <a href="?section=inventario&accion=eliminar&id=<?php echo $fila['inventario_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este ítem?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

