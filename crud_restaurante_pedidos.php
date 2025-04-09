<?php
// crud_restaurante_pedidos.php

$accion = $_GET['accion'] ?? 'listar';
$pedido_id = $_GET['id'] ?? 0;

if ($_POST) {
    $mesa_id = $_POST['mesa_id'] ?? 0;
    $reserva_id = $_POST['reserva_id'] ?? null;
    $total = $_POST['total'] ?? 0.00;
    $estado_pedido = $_POST['estado_pedido'] ?? 'pendiente';

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO restaurante_pedidos (table_id, reservation_id, total, order_status)
                                  VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $mesa_id, $reserva_id, $total, $estado_pedido);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=restaurante_pedidos");
        exit;
    } elseif ($accion == 'editar' && $pedido_id > 0) {
        $stmt = $mysqli->prepare("UPDATE restaurante_pedidos
                                  SET table_id=?, reservation_id=?, total=?, order_status=?
                                  WHERE order_id=?");
        $stmt->bind_param("iidsi", $mesa_id, $reserva_id, $total, $estado_pedido, $pedido_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=restaurante_pedidos");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $pedido_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM restaurante_pedidos WHERE order_id=?");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=restaurante_pedidos");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $mesas = $mysqli->query("SELECT table_id, table_name FROM restaurante_mesas");
    $reservas = $mysqli->query("SELECT reserva_id FROM reservas");
    ?>
    <h3>Crear Pedido</h3>
    <form method="post" action="?section=restaurante_pedidos&accion=crear">
        <label>Mesa:
            <select name="mesa_id">
                <?php while($m = $mesas->fetch_assoc()): ?>
                    <option value="<?php echo $m['table_id']; ?>">
                        <?php echo htmlspecialchars($m['table_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Reserva (opcional):
            <select name="reserva_id">
                <option value="">--Ninguna--</option>
                <?php while($r = $reservas->fetch_assoc()): ?>
                    <option value="<?php echo $r['reserva_id']; ?>">
                        <?php echo $r['reserva_id']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Total:
            <input type="number" step="0.01" name="total" value="0.00">
        </label><br><br>
        <label>Estado Pedido:
            <input type="text" name="estado_pedido" value="pendiente">
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $pedido_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM restaurante_pedidos WHERE order_id=?");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $pedido = $res->fetch_assoc();
    $stmt->close();

    if (!$pedido) {
        echo "<p>Pedido no encontrado</p>";
        return;
    }

    $mesas = $mysqli->query("SELECT table_id, table_name FROM restaurante_mesas");
    $reservas = $mysqli->query("SELECT reserva_id FROM reservas");
    ?>
    <h3>Editar Pedido</h3>
    <form method="post" action="?section=restaurante_pedidos&accion=editar&id=<?php echo $pedido_id; ?>">
        <label>Mesa:
            <select name="mesa_id">
                <?php while($m = $mesas->fetch_assoc()): ?>
                    <option value="<?php echo $m['table_id']; ?>"
                        <?php if($m['table_id'] == $pedido['table_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($m['table_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Reserva (opcional):
            <select name="reserva_id">
                <option value="">--Ninguna--</option>
                <?php 
                mysqli_data_seek($reservas, 0);
                while($r = $reservas->fetch_assoc()): 
                ?>
                    <option value="<?php echo $r['reserva_id']; ?>"
                        <?php if($r['reserva_id'] == $pedido['reservation_id']) echo 'selected'; ?>>
                        <?php echo $r['reserva_id']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Total:
            <input type="number" step="0.01" name="total" value="<?php echo htmlspecialchars($pedido['total']); ?>">
        </label><br><br>
        <label>Estado Pedido:
            <input type="text" name="estado_pedido" value="<?php echo htmlspecialchars($pedido['order_status']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT rp.*, rm.table_name, re.estado as estado_reserva
                          FROM restaurante_pedidos rp
                          JOIN restaurante_mesas rm ON rp.table_id = rm.table_id
                          LEFT JOIN reservas re ON rp.reservation_id = re.reserva_id
                          ORDER BY rp.order_id DESC");
?>
<h3>Pedidos de Restaurante</h3>
<p><a href="?section=restaurante_pedidos&accion=crear" class="actualizar">+ Crear Pedido</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Mesa</th>
            <th>Reserva</th>
            <th>Total</th>
            <th>Estado Pedido</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['order_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['table_name']); ?></td>
            <td><?php echo htmlspecialchars($fila['reservation_id']); ?> (<?php echo htmlspecialchars($fila['estado_reserva']); ?>)</td>
            <td><?php echo $fila['total']; ?></td>
            <td><?php echo htmlspecialchars($fila['order_status']); ?></td>
            <td>
                <a href="?section=restaurante_pedidos&accion=editar&id=<?php echo $fila['order_id']; ?>" class="actualizar">✏</a>
                <a href="?section=restaurante_pedidos&accion=eliminar&id=<?php echo $fila['order_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este pedido?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

