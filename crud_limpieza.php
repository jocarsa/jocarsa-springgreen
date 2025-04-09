<?php
// crud_limpieza.php

$accion = $_GET['accion'] ?? 'listar';
$limpieza_id = $_GET['id'] ?? 0;

if ($_POST) {
    $propiedad_id = $_POST['propiedad_id'] ?? 0;
    $habitacion_id = $_POST['habitacion_id'] ?? 0;
    $personal_id = $_POST['personal_id'] ?? 0;
    $fecha_tarea = $_POST['fecha_tarea'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';
    $notas = $_POST['notas'] ?? '';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO limpieza (propiedad_id, room_id, staff_id, task_date, status, notes)
                                  VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisss", $propiedad_id, $habitacion_id, $personal_id, $fecha_tarea, $estado, $notas);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=limpieza");
        exit;
    } elseif ($accion == 'editar' && $limpieza_id > 0) {
        $stmt = $mysqli->prepare("UPDATE limpieza
                                  SET propiedad_id=?, room_id=?, staff_id=?, task_date=?, status=?, notes=?
                                  WHERE limpieza_id=?");
        $stmt->bind_param("iiisssi", $propiedad_id, $habitacion_id, $personal_id, $fecha_tarea, $estado, $notas, $limpieza_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=limpieza");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $limpieza_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM limpieza WHERE limpieza_id=?");
    $stmt->bind_param("i", $limpieza_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=limpieza");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    $habitaciones = $mysqli->query("SELECT habitacion_id, numero_habitacion FROM habitaciones");
    $personal = $mysqli->query("SELECT personal_id, nombre, apellidos FROM personal");
    ?>
    <h3>Crear Registro de Limpieza</h3>
    <form method="post" action="?section=limpieza&accion=crear">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>">
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Habitación:
            <select name="habitacion_id">
                <?php while($h = $habitaciones->fetch_assoc()): ?>
                    <option value="<?php echo $h['habitacion_id']; ?>">
                        <?php echo htmlspecialchars($h['numero_habitacion']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Personal:
            <select name="personal_id">
                <?php while($st = $personal->fetch_assoc()): ?>
                    <option value="<?php echo $st['personal_id']; ?>">
                        <?php echo htmlspecialchars($st['nombre']." ".$st['apellidos']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Fecha Tarea:
            <input type="date" name="fecha_tarea">
        </label><br><br>
        <label>Estado:
            <input type="text" name="estado" value="pendiente">
        </label><br><br>
        <label>Notas:
            <textarea name="notas"></textarea>
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $limpieza_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM limpieza WHERE limpieza_id=?");
    $stmt->bind_param("i", $limpieza_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $limp = $res->fetch_assoc();
    $stmt->close();

    if (!$limp) {
        echo "<p>Registro de limpieza no encontrado</p>";
        return;
    }

    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    $habitaciones = $mysqli->query("SELECT habitacion_id, numero_habitacion FROM habitaciones");
    $personal = $mysqli->query("SELECT personal_id, nombre, apellidos FROM personal");
    ?>
    <h3>Editar Registro de Limpieza</h3>
    <form method="post" action="?section=limpieza&accion=editar&id=<?php echo $limpieza_id; ?>">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"
                        <?php if($p['propiedad_id'] == $limp['property_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Habitación:
            <select name="habitacion_id">
                <?php 
                mysqli_data_seek($habitaciones, 0);
                while($h = $habitaciones->fetch_assoc()): 
                ?>
                    <option value="<?php echo $h['habitacion_id']; ?>"
                        <?php if($h['habitacion_id'] == $limp['room_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($h['numero_habitacion']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Personal:
            <select name="personal_id">
                <?php 
                mysqli_data_seek($personal, 0);
                while($st = $personal->fetch_assoc()): 
                ?>
                    <option value="<?php echo $st['personal_id']; ?>"
                        <?php if($st['personal_id'] == $limp['staff_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($st['nombre']." ".$st['apellidos']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Fecha Tarea:
            <input type="date" name="fecha_tarea" value="<?php echo htmlspecialchars($limp['task_date']); ?>">
        </label><br><br>
        <label>Estado:
            <input type="text" name="estado" value="<?php echo htmlspecialchars($limp['status']); ?>">
        </label><br><br>
        <label>Notas:
            <textarea name="notas"><?php echo htmlspecialchars($limp['notes']); ?></textarea>
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT l.*, p.nombre_propiedad, h.numero_habitacion, s.nombre AS nombre_staff, s.apellidos AS apellido_staff
                          FROM limpieza l
                          JOIN propiedades p ON l.property_id = p.propiedad_id
                          JOIN habitaciones h ON l.room_id = h.habitacion_id
                          JOIN personal s ON l.staff_id = s.personal_id
                          ORDER BY l.limpieza_id DESC");
?>
<h3>Limpieza</h3>
<p><a href="?section=limpieza&accion=crear" class="actualizar">+ Crear Registro de Limpieza</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Propiedad</th>
            <th>Habitación</th>
            <th>Personal</th>
            <th>Fecha Tarea</th>
            <th>Estado</th>
            <th>Notas</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['limpieza_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
            <td><?php echo htmlspecialchars($fila['numero_habitacion']); ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_staff']." ".$fila['apellido_staff']); ?></td>
            <td><?php echo htmlspecialchars($fila['task_date']); ?></td>
            <td><?php echo htmlspecialchars($fila['status']); ?></td>
            <td><?php echo htmlspecialchars($fila['notes']); ?></td>
            <td>
                <a href="?section=limpieza&accion=editar&id=<?php echo $fila['limpieza_id']; ?>" class="actualizar">✏</a>
                <a href="?section=limpieza&accion=eliminar&id=<?php echo $fila['limpieza_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este registro de limpieza?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

