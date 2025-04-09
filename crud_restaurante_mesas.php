<?php
// crud_restaurante_mesas.php

$accion = $_GET['accion'] ?? 'listar';
$mesa_id = $_GET['id'] ?? 0;

if ($_POST) {
    $propiedad_id = $_POST['propiedad_id'] ?? 0;
    $nombre_mesa = $_POST['nombre_mesa'] ?? '';
    $capacidad = $_POST['capacidad'] ?? 0;

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO restaurante_mesas (propiedad_id, table_name, capacity)
                                  VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $propiedad_id, $nombre_mesa, $capacidad);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=restaurante_mesas");
        exit;
    } elseif ($accion == 'editar' && $mesa_id > 0) {
        $stmt = $mysqli->prepare("UPDATE restaurante_mesas
                                  SET propiedad_id=?, table_name=?, capacity=?
                                  WHERE table_id=?");
        $stmt->bind_param("isii", $propiedad_id, $nombre_mesa, $capacidad, $mesa_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=restaurante_mesas");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $mesa_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM restaurante_mesas WHERE table_id=?");
    $stmt->bind_param("i", $mesa_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=restaurante_mesas");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Crear Mesa de Restaurante</h3>
    <form method="post" action="?section=restaurante_mesas&accion=crear">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>">
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Nombre de Mesa:
            <input type="text" name="nombre_mesa">
        </label><br><br>
        <label>Capacidad:
            <input type="number" name="capacidad" value="4">
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $mesa_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM restaurante_mesas WHERE table_id=?");
    $stmt->bind_param("i", $mesa_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $mesa = $res->fetch_assoc();
    $stmt->close();

    if (!$mesa) {
        echo "<p>Mesa no encontrada</p>";
        return;
    }

    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Editar Mesa de Restaurante</h3>
    <form method="post" action="?section=restaurante_mesas&accion=editar&id=<?php echo $mesa_id; ?>">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"
                        <?php if($p['propiedad_id'] == $mesa['propiedad_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Nombre de Mesa:
            <input type="text" name="nombre_mesa" value="<?php echo htmlspecialchars($mesa['table_name']); ?>">
        </label><br><br>
        <label>Capacidad:
            <input type="number" name="capacidad" value="<?php echo htmlspecialchars($mesa['capacity']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT rm.*, p.nombre_propiedad
                          FROM restaurante_mesas rm
                          JOIN propiedades p ON rm.propiedad_id = p.propiedad_id
                          ORDER BY rm.table_id DESC");
?>
<h3>Mesas de Restaurante</h3>
<p><a href="?section=restaurante_mesas&accion=crear" class="actualizar">+ Crear Mesa</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Propiedad</th>
            <th>Nombre Mesa</th>
            <th>Capacidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['table_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
            <td><?php echo htmlspecialchars($fila['table_name']); ?></td>
            <td><?php echo $fila['capacity']; ?></td>
            <td>
                <a href="?section=restaurante_mesas&accion=editar&id=<?php echo $fila['table_id']; ?>" class="actualizar">✏</a>
                <a href="?section=restaurante_mesas&accion=eliminar&id=<?php echo $fila['table_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar esta mesa?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

