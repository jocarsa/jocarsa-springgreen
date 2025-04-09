<?php
// crud_habitaciones.php

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';
$habitacion_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// CREAR / EDITAR
if ($_POST) {
    $propiedad_id = $_POST['propiedad_id'] ?? 0;
    $tipo_habitacion_id = $_POST['tipo_habitacion_id'] ?? 0;
    $numero = $_POST['numero_habitacion'] ?? '';
    $capacidad = $_POST['capacidad'] ?? 1;
    $estado = $_POST['estado'] ?? 'disponible';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO habitaciones (propiedad_id, tipo_habitacion_id, numero_habitacion, capacidad, estado)
          VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisds", $propiedad_id, $tipo_habitacion_id, $numero, $capacidad, $estado);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=habitaciones");
        exit;
    } elseif ($accion == 'editar' && $habitacion_id > 0) {
        $stmt = $mysqli->prepare("UPDATE habitaciones
            SET propiedad_id=?, tipo_habitacion_id=?, numero_habitacion=?, capacidad=?, estado=?
            WHERE habitacion_id=?");
        $stmt->bind_param("iisdsi", $propiedad_id, $tipo_habitacion_id, $numero, $capacidad, $estado, $habitacion_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=habitaciones");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $habitacion_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM habitaciones WHERE habitacion_id=?");
    $stmt->bind_param("i", $habitacion_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=habitaciones");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    // Necesitamos listar propiedades y tipos de habitación para el <select>
    $prop_result = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    $tipo_result = $mysqli->query("SELECT tipo_habitacion_id, nombre_tipo FROM tipos_habitacion");
    ?>
    <h3>Crear Habitación</h3>
    <form method="post" action="?section=habitaciones&accion=crear">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $prop_result->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"><?php echo htmlspecialchars($p['nombre_propiedad']); ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Tipo de Habitación:
            <select name="tipo_habitacion_id">
                <?php while($t = $tipo_result->fetch_assoc()): ?>
                    <option value="<?php echo $t['tipo_habitacion_id']; ?>"><?php echo htmlspecialchars($t['nombre_tipo']); ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Número de Habitación:
            <input type="text" name="numero_habitacion" required>
        </label><br><br>

        <label>Capacidad:
            <input type="number" name="capacidad" value="1" required>
        </label><br><br>

        <label>Estado:
            <input type="text" name="estado" value="disponible">
        </label><br><br>

        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $habitacion_id > 0) {
    // cargamos la habitación
    $stmt = $mysqli->prepare("SELECT * FROM habitaciones WHERE habitacion_id=?");
    $stmt->bind_param("i", $habitacion_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $hab = $res->fetch_assoc();
    $stmt->close();

    if (!$hab) {
        echo "<p>Habitación no encontrada</p>";
        return;
    }

    // listas para selects
    $prop_result = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    $tipo_result = $mysqli->query("SELECT tipo_habitacion_id, nombre_tipo FROM tipos_habitacion");
    ?>
    <h3>Editar Habitación</h3>
    <form method="post" action="?section=habitaciones&accion=editar&id=<?php echo $habitacion_id; ?>">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $prop_result->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>" 
                        <?php if ($p['propiedad_id'] == $hab['propiedad_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Tipo de Habitación:
            <select name="tipo_habitacion_id">
                <?php while($t = $tipo_result->fetch_assoc()): ?>
                    <option value="<?php echo $t['tipo_habitacion_id']; ?>"
                        <?php if($t['tipo_habitacion_id'] == $hab['tipo_habitacion_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($t['nombre_tipo']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Número de Habitación:
            <input type="text" name="numero_habitacion" value="<?php echo htmlspecialchars($hab['numero_habitacion']); ?>" required>
        </label><br><br>

        <label>Capacidad:
            <input type="number" name="capacidad" value="<?php echo htmlspecialchars($hab['capacidad']); ?>" required>
        </label><br><br>

        <label>Estado:
            <input type="text" name="estado" value="<?php echo htmlspecialchars($hab['estado']); ?>">
        </label><br><br>

        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT h.*, p.nombre_propiedad, t.nombre_tipo 
                          FROM habitaciones h
                          JOIN propiedades p ON h.propiedad_id = p.propiedad_id
                          JOIN tipos_habitacion t ON h.tipo_habitacion_id = t.tipo_habitacion_id
                          ORDER BY habitacion_id DESC");
?>
<h3>Habitaciones</h3>
<p><a href="?section=habitaciones&accion=crear" class="actualizar">+ Crear Nueva Habitación</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Propiedad</th>
            <th>Tipo Habitación</th>
            <th>Número</th>
            <th>Capacidad</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($fila = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila['habitacion_id']; ?></td>
                <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
                <td><?php echo htmlspecialchars($fila['nombre_tipo']); ?></td>
                <td><?php echo htmlspecialchars($fila['numero_habitacion']); ?></td>
                <td><?php echo htmlspecialchars($fila['capacidad']); ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <a href="?section=habitaciones&accion=editar&id=<?php echo $fila['habitacion_id']; ?>" class="actualizar">✏</a>
                    <a href="?section=habitaciones&accion=eliminar&id=<?php echo $fila['habitacion_id']; ?>" class="eliminar"
                       onclick="return confirm('¿Eliminar esta habitación?');">❌</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

