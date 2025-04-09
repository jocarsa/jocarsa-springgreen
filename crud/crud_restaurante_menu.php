<?php
// crud_restaurante_menu.php

$accion = $_GET['accion'] ?? 'listar';
$menu_id = $_GET['id'] ?? 0;

if ($_POST) {
    $nombre_plato = $_POST['nombre_plato'] ?? '';
    $precio = $_POST['precio'] ?? 0.00;
    $descripcion = $_POST['descripcion'] ?? '';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO restaurante_menu (dish_name, price, description)
                                  VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $nombre_plato, $precio, $descripcion);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=restaurante_menu");
        exit;
    } elseif ($accion == 'editar' && $menu_id > 0) {
        $stmt = $mysqli->prepare("UPDATE restaurante_menu
                                  SET dish_name=?, price=?, description=?
                                  WHERE menu_id=?");
        $stmt->bind_param("sdsi", $nombre_plato, $precio, $descripcion, $menu_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=restaurante_menu");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $menu_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM restaurante_menu WHERE menu_id=?");
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=restaurante_menu");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    ?>
    <h3>Crear Plato</h3>
    <form method="post" action="?section=restaurante_menu&accion=crear">
        <label>Nombre del Plato:
            <input type="text" name="nombre_plato" required>
        </label><br><br>
        <label>Precio:
            <input type="number" step="0.01" name="precio" value="0.00">
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
if ($accion == 'editar' && $menu_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM restaurante_menu WHERE menu_id=?");
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $plato = $res->fetch_assoc();
    $stmt->close();

    if (!$plato) {
        echo "<p>Plato no encontrado</p>";
        return;
    }
    ?>
    <h3>Editar Plato</h3>
    <form method="post" action="?section=restaurante_menu&accion=editar&id=<?php echo $menu_id; ?>">
        <label>Nombre del Plato:
            <input type="text" name="nombre_plato" value="<?php echo htmlspecialchars($plato['dish_name']); ?>" required>
        </label><br><br>
        <label>Precio:
            <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($plato['price']); ?>">
        </label><br><br>
        <label>Descripción:
            <textarea name="descripcion"><?php echo htmlspecialchars($plato['description']); ?></textarea>
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT * FROM restaurante_menu ORDER BY menu_id DESC");
?>
<h3>Menú de Restaurante</h3>
<p><a href="?section=restaurante_menu&accion=crear" class="actualizar">+ Crear Plato</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Plato</th>
            <th>Precio</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['menu_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['dish_name']); ?></td>
            <td><?php echo $fila['price']; ?></td>
            <td><?php echo htmlspecialchars($fila['description']); ?></td>
            <td>
                <a href="?section=restaurante_menu&accion=editar&id=<?php echo $fila['menu_id']; ?>" class="actualizar">✏</a>
                <a href="?section=restaurante_menu&accion=eliminar&id=<?php echo $fila['menu_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este plato?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

