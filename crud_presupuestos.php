<?php
// crud_presupuestos.php

$accion = $_GET['accion'] ?? 'listar';
$presupuesto_id = $_GET['id'] ?? 0;

if ($_POST) {
    $propiedad_id = $_POST['propiedad_id'] ?? 0;
    $anio = $_POST['anio'] ?? date('Y');
    $monto_total = $_POST['monto_total'] ?? 0.00;

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO presupuestos (propiedad_id, year, total_amount)
                                  VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $propiedad_id, $anio, $monto_total);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=presupuestos");
        exit;
    } elseif ($accion == 'editar' && $presupuesto_id > 0) {
        $stmt = $mysqli->prepare("UPDATE presupuestos
                                  SET propiedad_id=?, year=?, total_amount=?
                                  WHERE budget_id=?");
        $stmt->bind_param("iidi", $propiedad_id, $anio, $monto_total, $presupuesto_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=presupuestos");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $presupuesto_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM presupuestos WHERE budget_id=?");
    $stmt->bind_param("i", $presupuesto_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=presupuestos");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Crear Presupuesto</h3>
    <form method="post" action="?section=presupuestos&accion=crear">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>">
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Año:
            <input type="number" name="anio" value="<?php echo date('Y'); ?>">
        </label><br><br>
        <label>Monto Total:
            <input type="number" step="0.01" name="monto_total" value="0.00">
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $presupuesto_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM presupuestos WHERE budget_id=?");
    $stmt->bind_param("i", $presupuesto_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $presupuesto = $res->fetch_assoc();
    $stmt->close();

    if (!$presupuesto) {
        echo "<p>Presupuesto no encontrado</p>";
        return;
    }

    $propiedades = $mysqli->query("SELECT propiedad_id, nombre_propiedad FROM propiedades");
    ?>
    <h3>Editar Presupuesto</h3>
    <form method="post" action="?section=presupuestos&accion=editar&id=<?php echo $presupuesto_id; ?>">
        <label>Propiedad:
            <select name="propiedad_id">
                <?php while($p = $propiedades->fetch_assoc()): ?>
                    <option value="<?php echo $p['propiedad_id']; ?>"
                        <?php if($p['propiedad_id'] == $presupuesto['property_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre_propiedad']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>
        <label>Año:
            <input type="number" name="anio" value="<?php echo htmlspecialchars($presupuesto['year']); ?>">
        </label><br><br>
        <label>Monto Total:
            <input type="number" step="0.01" name="monto_total" value="<?php echo htmlspecialchars($presupuesto['total_amount']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT pr.*, p.nombre_propiedad
                          FROM presupuestos pr
                          JOIN propiedades p ON pr.property_id = p.propiedad_id
                          ORDER BY pr.budget_id DESC");
?>
<h3>Presupuestos</h3>
<p><a href="?section=presupuestos&accion=crear" class="actualizar">+ Crear Presupuesto</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Propiedad</th>
            <th>Año</th>
            <th>Monto Total</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['budget_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['nombre_propiedad']); ?></td>
            <td><?php echo $fila['year']; ?></td>
            <td><?php echo $fila['total_amount']; ?></td>
            <td>
                <a href="?section=presupuestos&accion=editar&id=<?php echo $fila['budget_id']; ?>" class="actualizar">✏</a>
                <a href="?section=presupuestos&accion=eliminar&id=<?php echo $fila['budget_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este presupuesto?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

