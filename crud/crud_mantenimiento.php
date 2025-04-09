<?php
// crud_mantenimiento.php

$accion = $_GET['accion'] ?? 'listar';
$mantenimiento_id = $_GET['id'] ?? 0;

if ($_POST) {
    $propiedad_id = $_POST['propiedad_id'] ?? 0;
    $habitacion_id = $_POST['habitacion_id'] ?? null;
    $descripcion_incidencia = $_POST['descripcion_incidencia'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';
    $prioridad = $_POST['prioridad'] ?? 'media';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO mantenimiento (propiedad_id, room_id, issue_description, status, priority)
                                  VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $propiedad_id, $habitacion_id, $descripcion_incidencia, $estado, $prioridad);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=mantenimiento");
        exit;
    } elseif ($accion == 'editar' && $mantenimiento_id > 0) {
        $stmt = $mysqli->prepare("UPDATE mantenimiento
                                  SET propiedad_id=?, room_id=?, issue_description=?, status=?, priority=?
                                  WHERE maintenance_id=?");
        $stmt->bind_param("iisssi", $propiedad_id, $habitacion_id, $descripcion_incidencia, $estado, $prioridad, $mantenimiento_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=mantenimiento");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $mantenimiento_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM mantenimiento WHERE maintenance_id=?");
    $stmt->bind_param("i", $mantenimiento_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=mantenimiento");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    $habitaciones = $mysqli->query("SELECT habitacion_id, numero_habitacion FROM habitaciones");
    ?>
    <h3>Crear Mantenimiento</h3>
    <form method="post" action="?section=mantenimiento&accion=crear">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>">
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Habitación (opcional):
            <select name="habitacion_id">
                <option value="">--No aplicar a una habitación específica--</option>
                <?php while($h = $habitaciones->fetch_assoc()): ?>
                    <option value="<?php echo $h['habitacion_id']; ?>">
                        <?php echo htmlspecialchars($h['numero_habitacion']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Descripción Incidencia:
            <textarea name="descripcion_incidencia"></textarea>
        </label><br><br>

        <label>Estado:
            <input type="text" name="estado" value="pendiente">
        </label><br><br>

        <label>Prioridad:
            <input type="text" name="prioridad" value="media">
        </label><br><br>

        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $mantenimiento_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM mantenimiento WHERE maintenance_id=?");
    $stmt->bind_param("i", $mantenimiento_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $mant = $res->fetch_assoc();
    $stmt->close();

    if (!$mant) {
        echo "<p>Registro de mantenimiento no encontrado</p>";
        return;
    }

    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    $habitaciones = $mysqli->query("SELECT habitacion_id, numero_habitacion FROM habitaciones");
    ?>
    <h3>Editar Mantenimiento</h3>
    <form method="post" action="?section=mantenimiento&accion=editar&id=<?php echo $mantenimiento_id; ?>">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"
                        <?php if($p['propiedad_id'] == $mant['property_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Habitación (opcional):
            <select name="habitacion_id">
                <option value="">--No aplicar a una habitación específica--</option>
                <?php 
                mysqli_data_seek($habitaciones, 0); // reiniciar cursor
                while($h = $habitaciones->fetch_assoc()): 
                ?>
                    <option value="<?php echo $h['habitacion_id']; ?>"
                        <?php if($h['habitacion_id'] == $mant['room_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($h['numero_habitacion']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Descripción Incidencia:
            <textarea name="descripcion_incidencia"><?php echo htmlspecialchars($mant['issue_description']); ?></textarea>
        </label><br><br>

        <label>Estado:
            <input type="text" name="estado" value="<?php echo htmlspecialchars($mant['status']); ?>">
        </label><br><br>

        <label>Prioridad:
            <input type="text" name="prioridad" value="<?php echo htmlspecialchars($mant['priority']); ?>">
        </label><br><br>

        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT m.*, p.nombre_propiedad, h.numero_habitacion 
                          FROM mantenimiento m
                          JOIN propiedades p ON m.property_id = p.propiedad_id
                          LEFT JOIN habitaciones h ON m.room_id = h.habitacion_id
                          ORDER BY m.maintenance_id DESC");
?>
<h3>Mantenimiento</h3>
<p><a href="?section=mantenimiento&accion=crear" class="actualizar">+ Crear Entrada de Mantenimiento</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Propiedad</th>
            <th>Habitación</th>
            <th>Incidencia</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['maintenance_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
            <td><?php echo htmlspecialchars($fila['numero_habitacion']); ?></td>
            <td><?php echo htmlspecialchars($fila['issue_description']); ?></td>
            <td><?php echo htmlspecialchars($fila['status']); ?></td>
            <td><?php echo htmlspecialchars($fila['priority']); ?></td>
            <td>
                <a href="?section=mantenimiento&accion=editar&id=<?php echo $fila['maintenance_id']; ?>" class="actualizar">✏</a>
                <a href="?section=mantenimiento&accion=eliminar&id=<?php echo $fila['maintenance_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este registro de mantenimiento?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

