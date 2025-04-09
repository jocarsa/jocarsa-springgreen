<?php
// crud_propiedades.php
// Asumimos que ya existe la conexión $mysqli definida en index.php

// 1. Manejo de acciones (crear/editar/eliminar) /////////////////////
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';
$propiedad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si hay POST de crear/actualizar
if ($_POST) {
    // Recibimos datos del formulario
    $nombre_propiedad = $_POST['nombre_propiedad'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $pais = $_POST['pais'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($accion == 'crear') {
        // Insertamos en la tabla
        $stmt = $mysqli->prepare("INSERT INTO propiedades 
            (nombre_propiedad, direccion, ciudad, pais, telefono, email) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre_propiedad, $direccion, $ciudad, $pais, $telefono, $email);
        $stmt->execute();
        $stmt->close();
        // Redireccionar a la lista
        header("Location: ?section=propiedades");
        exit;
    } elseif ($accion == 'editar' && $propiedad_id > 0) {
        // Actualizamos
        $stmt = $mysqli->prepare("UPDATE propiedades
            SET nombre_propiedad=?, direccion=?, ciudad=?, pais=?, telefono=?, email=?
            WHERE propiedad_id=?");
        $stmt->bind_param("ssssssi", 
            $nombre_propiedad, $direccion, $ciudad, $pais, $telefono, $email, $propiedad_id);
        $stmt->execute();
        $stmt->close();
        // Redireccionamos
        header("Location: ?section=propiedades");
        exit;
    }
}

// 2. Eliminar (si accion=eliminar y hay id)
if ($accion == 'eliminar' && $propiedad_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM propiedades WHERE propiedad_id=?");
    $stmt->bind_param("i", $propiedad_id);
    $stmt->execute();
    $stmt->close();
    // Redireccionar al listado
    header("Location: ?section=propiedades");
    exit;
}

// 3. Si es crear o editar, mostramos formulario
if ($accion == 'crear') {
    // Form en blanco
    $propiedad = [
        'propiedad_id' => 0,
        'nombre_propiedad' => '',
        'direccion' => '',
        'ciudad' => '',
        'pais' => '',
        'telefono' => '',
        'email' => ''
    ];
    ?>
    <h3>Crear Propiedad</h3>
    <form method="post" action="?section=propiedades&accion=crear">
        <label>Nombre: <input type="text" name="nombre_propiedad" required></label><br><br>
        <label>Dirección: <input type="text" name="direccion"></label><br><br>
        <label>Ciudad: <input type="text" name="ciudad"></label><br><br>
        <label>País: <input type="text" name="pais"></label><br><br>
        <label>Teléfono: <input type="text" name="telefono"></label><br><br>
        <label>Email: <input type="email" name="email"></label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return; // Salimos para no mostrar la lista
} elseif ($accion == 'editar' && $propiedad_id > 0) {
    // Recuperamos datos de la propiedad
    $stmt = $mysqli->prepare("SELECT * FROM propiedades WHERE propiedad_id=?");
    $stmt->bind_param("i", $propiedad_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $propiedad = $resultado->fetch_assoc();
    $stmt->close();
    if (!$propiedad) {
        echo "<p>Propiedad no encontrada</p>";
        return;
    }
    ?>
    <h3>Editar Propiedad</h3>
    <form method="post" action="?section=propiedades&accion=editar&id=<?php echo $propiedad_id; ?>">
        <label>Nombre: 
            <input type="text" name="nombre_propiedad" value="<?php echo htmlspecialchars($propiedad['nombre_propiedad']); ?>" required>
        </label><br><br>
        <label>Dirección:
            <input type="text" name="direccion" value="<?php echo htmlspecialchars($propiedad['direccion']); ?>">
        </label><br><br>
        <label>Ciudad:
            <input type="text" name="ciudad" value="<?php echo htmlspecialchars($propiedad['ciudad']); ?>">
        </label><br><br>
        <label>País:
            <input type="text" name="pais" value="<?php echo htmlspecialchars($propiedad['pais']); ?>">
        </label><br><br>
        <label>Teléfono:
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($propiedad['telefono']); ?>">
        </label><br><br>
        <label>Email:
            <input type="email" name="email" value="<?php echo htmlspecialchars($propiedad['email']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return; // Salimos para no mostrar la lista
}

// 4. Si no hay accion o es 'listar', mostramos la tabla de propiedades
$result = $mysqli->query("SELECT * FROM propiedades ORDER BY propiedad_id DESC");
?>
<h3>Propiedades</h3>
<p><a href="?section=propiedades&accion=crear" class="actualizar">+ Crear Nueva Propiedad</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Ciudad</th>
            <th>País</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($fila = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila['propiedad_id']; ?></td>
                <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
                <td><?php echo htmlspecialchars($fila['direccion']); ?></td>
                <td><?php echo htmlspecialchars($fila['ciudad']); ?></td>
                <td><?php echo htmlspecialchars($fila['pais']); ?></td>
                <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                <td><?php echo htmlspecialchars($fila['email']); ?></td>
                <td>
                    <a href="?section=propiedades&accion=editar&id=<?php echo $fila['propiedad_id']; ?>" class="actualizar">✏</a>
                    <a href="?section=propiedades&accion=eliminar&id=<?php echo $fila['propiedad_id']; ?>" class="eliminar"
                       onclick="return confirm('¿Estás seguro de eliminar esta propiedad?');">
                       ❌
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

