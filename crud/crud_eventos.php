<?php
// crud_eventos.php

$accion = $_GET['accion'] ?? 'listar';
$evento_id = $_GET['id'] ?? 0;

// CREAR / EDITAR
if ($_POST) {
    $propiedad_id   = $_POST['propiedad_id']   ?? 0;
    $nombre_evento  = $_POST['nombre_evento']  ?? '';
    $fecha_inicio   = $_POST['fecha_inicio']   ?? '';
    $fecha_fin      = $_POST['fecha_fin']      ?? '';
    $estado         = $_POST['estado']         ?? 'planificado';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("
            INSERT INTO eventos (propiedad_id, nombre_evento, start_date, end_date, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issss", $propiedad_id, $nombre_evento, $fecha_inicio, $fecha_fin, $estado);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=eventos");
        exit;

    } elseif ($accion == 'editar' && $evento_id > 0) {
        $stmt = $mysqli->prepare("
            UPDATE eventos
            SET propiedad_id = ?, nombre_evento = ?, start_date = ?, end_date = ?, status = ?
            WHERE evento_id = ?
        ");
        $stmt->bind_param("issssi", $propiedad_id, $nombre_evento, $fecha_inicio, $fecha_fin, $estado, $evento_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=eventos");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $evento_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM eventos WHERE evento_id=?");
    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=eventos");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Crear Evento</h3>
    <form method="post" action="?section=eventos&accion=crear">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>">
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Nombre Evento:
            <input type="text" name="nombre_evento" required>
        </label><br><br>

        <label>Fecha Inicio:
            <input type="datetime-local" name="fecha_inicio">
        </label><br><br>

        <label>Fecha Fin:
            <input type="datetime-local" name="fecha_fin">
        </label><br><br>

        <label>Estado:
            <input type="text" name="estado" value="planificado">
        </label><br><br>

        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $evento_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM eventos WHERE evento_id=?");
    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $evento = $res->fetch_assoc();
    $stmt->close();

    if (!$evento) {
        echo "<p>Evento no encontrado</p>";
        return;
    }

    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Editar Evento</h3>
    <form method="post" action="?section=eventos&accion=editar&id=<?php echo $evento_id; ?>">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"
                        <?php if($p['propiedad_id'] == $evento['propiedad_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Nombre Evento:
            <input type="text" name="nombre_evento"
                   value="<?php echo htmlspecialchars($evento['nombre_evento']); ?>">
        </label><br><br>

        <label>Fecha Inicio:
            <input type="datetime-local" name="fecha_inicio"
                   value="<?php echo str_replace(' ', 'T', $evento['fecha_inicio']); ?>">
        </label><br><br>

        <label>Fecha Fin:
            <input type="datetime-local" name="fecha_fin"
                   value="<?php echo str_replace(' ', 'T', $evento['fecha_fin']); ?>">
        </label><br><br>

        <label>Estado:
            <input type="text" name="estado"
                   value="<?php echo htmlspecialchars($evento['estado']); ?>">
        </label><br><br>

        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("
    SELECT e.*, p.nombre_propiedad
    FROM eventos e
    JOIN propiedades p ON e.propiedad_id = p.propiedad_id
    ORDER BY e.evento_id DESC
");
?>
<h3>Eventos</h3>
<p><a href="?section=eventos&accion=crear" class="actualizar">+ Crear Nuevo Evento</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Propiedad</th>
            <th>Nombre</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['evento_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_evento']); ?></td>
            <td><?php echo htmlspecialchars($fila['fecha_inicio']); ?></td>
            <td><?php echo htmlspecialchars($fila['fecha_fin']); ?></td>
            <td><?php echo htmlspecialchars($fila['estado']); ?></td>
            <td>
                <a href="?section=eventos&accion=editar&id=<?php echo $fila['evento_id']; ?>" class="actualizar">✏</a>
                <a href="?section=eventos&accion=eliminar&id=<?php echo $fila['evento_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este evento?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

