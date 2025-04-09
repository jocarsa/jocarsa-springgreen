<?php
// crud_huespedes.php

$accion = $_GET['accion'] ?? 'listar';
$huesped_id = $_GET['id'] ?? 0;

// CREAR/EDITAR
if ($_POST) {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $doc = $_POST['documento_identidad'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO huespedes 
            (nombre, apellidos, documento_identidad, email, telefono)
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $apellidos, $doc, $email, $telefono);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=huespedes");
        exit;
    } elseif ($accion == 'editar' && $huesped_id > 0) {
        $stmt = $mysqli->prepare("UPDATE huespedes SET 
            nombre=?, apellidos=?, documento_identidad=?, email=?, telefono=?
            WHERE huesped_id=?");
        $stmt->bind_param("sssssi", $nombre, $apellidos, $doc, $email, $telefono, $huesped_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=huespedes");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $huesped_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM huespedes WHERE huesped_id=?");
    $stmt->bind_param("i", $huesped_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=huespedes");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    ?>
    <h3>Crear Huésped</h3>
    <form method="post" action="?section=huespedes&accion=crear">
        <label>Nombre: <input type="text" name="nombre" required></label><br><br>
        <label>Apellidos: <input type="text" name="apellidos"></label><br><br>
        <label>Documento: <input type="text" name="documento_identidad"></label><br><br>
        <label>Email: <input type="email" name="email"></label><br><br>
        <label>Teléfono: <input type="text" name="telefono"></label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $huesped_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM huespedes WHERE huesped_id=?");
    $stmt->bind_param("i", $huesped_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $huesped = $res->fetch_assoc();
    $stmt->close();

    if (!$huesped) {
        echo "<p>Huésped no encontrado</p>";
        return;
    }
    ?>
    <h3>Editar Huésped</h3>
    <form method="post" action="?section=huespedes&accion=editar&id=<?php echo $huesped_id; ?>">
        <label>Nombre:
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($huesped['nombre']); ?>">
        </label><br><br>
        <label>Apellidos:
            <input type="text" name="apellidos" value="<?php echo htmlspecialchars($huesped['apellidos']); ?>">
        </label><br><br>
        <label>Documento:
            <input type="text" name="documento_identidad" value="<?php echo htmlspecialchars($huesped['documento_identidad']); ?>">
        </label><br><br>
        <label>Email:
            <input type="email" name="email" value="<?php echo htmlspecialchars($huesped['email']); ?>">
        </label><br><br>
        <label>Teléfono:
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($huesped['telefono']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT * FROM huespedes ORDER BY huesped_id DESC");
?>
<h3>Huéspedes</h3>
<p><a href="?section=huespedes&accion=crear" class="actualizar">+ Crear Nuevo Huésped</a></p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Documento</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['huesped_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
            <td><?php echo htmlspecialchars($fila['apellidos']); ?></td>
            <td><?php echo htmlspecialchars($fila['documento_identidad']); ?></td>
            <td><?php echo htmlspecialchars($fila['email']); ?></td>
            <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
            <td>
                <a href="?section=huespedes&accion=editar&id=<?php echo $fila['huesped_id']; ?>" class="actualizar">✏</a>
                <a href="?section=huespedes&accion=eliminar&id=<?php echo $fila['huesped_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar a este huésped?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

