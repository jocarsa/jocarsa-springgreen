<?php
// crud_restaurante_pedidos_detalle.php

$accion = $_GET['accion'] ?? 'listar';
$detalle_id = $_GET['id'] ?? 0;

if ($_POST) {
    $order_id = $_POST['order_id'] ?? 0;
    $menu_id = $_POST['menu_id'] ?? 0;
    $cantidad = $_POST['cantidad'] ?? 1;
    $precio_item = $_POST['precio_item'] ?? 0.00;

    if ($accion == 'crear') {
        $stmt = $mysqli->prepare("INSERT INTO restaurante_order_items (order_id, menu_id, quantity, item_price)
                                  VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $menu_id, $cantidad, $precio_item);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=restaurante_pedidos_detalle");
        exit;
    } elseif ($accion == 'editar' && $detalle_id > 0) {
        $stmt = $mysqli->prepare("UPDATE restaurante_order_items
                                  SET order_id=?, menu_id=?, quantity=?, item_price=?
                                  WHERE order_item_id=?");
        $stmt->bind_param("iiidi", $order_id, $menu_id, $cantidad, $precio_item, $detalle_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?section=restaurante_pedidos_detalle");
        exit;
    }
}

// ELIMINAR
if ($accion == 'eliminar' && $detalle_id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM restaurante_order_items WHERE order_item_id=?");
    $stmt->bind_param("i", $detalle_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?section=restaurante_pedidos_detalle");
    exit;
}

// FORM CREAR
if ($accion == 'crear') {
    $pedidos = $mysqli->query("SELECT order_id FROM restaurante_pedidos");
    $menu = $mysqli->query("SELECT menu_id, dish_name, price FROM restaurante_menu");
    ?>
    <h3>Crear Detalle de Pedido</h3>
    <form method="post" action="?section=restaurante_pedidos_detalle&accion=crear">
        <label>Pedido:
            <select name="order_id">
                <?php while($p = $pedidos->fetch_assoc()): ?>
                    <option value="<?php echo $p['order_id']; ?>"><?php echo $p['order_id']; ?></option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Plato:
            <select name="menu_id" id="menu_select">
                <?php while($m = $menu->fetch_assoc()): ?>
                    <option value="<?php echo $m['menu_id']; ?>" data-price="<?php echo $m['price']; ?>">
                        <?php echo htmlspecialchars($m['dish_name']); ?> (<?php echo $m['price']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Cantidad:
            <input type="number" name="cantidad" value="1">
        </label><br><br>

        <label>Precio Item:
            <input type="number" step="0.01" name="precio_item" id="precio_item" value="0.00">
        </label><br><br>
        <button type="submit" class="actualizar">Crear</button>
    </form>
    <script>
        // Como ejemplo, al cambiar de plato, se puede auto-setear el precio
        document.querySelector("#menu_select").addEventListener("change", function(){
            let price = this.options[this.selectedIndex].getAttribute("data-price");
            document.querySelector("#precio_item").value = price;
        });
    </script>
    <?php
    return;
}

// FORM EDITAR
if ($accion == 'editar' && $detalle_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM restaurante_order_items WHERE order_item_id=?");
    $stmt->bind_param("i", $detalle_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $detalle = $res->fetch_assoc();
    $stmt->close();

    if (!$detalle) {
        echo "<p>Detalle no encontrado</p>";
        return;
    }

    $pedidos = $mysqli->query("SELECT order_id FROM restaurante_pedidos");
    $menu = $mysqli->query("SELECT menu_id, dish_name, price FROM restaurante_menu");
    ?>
    <h3>Editar Detalle de Pedido</h3>
    <form method="post" action="?section=restaurante_pedidos_detalle&accion=editar&id=<?php echo $detalle_id; ?>">
        <label>Pedido:
            <select name="order_id">
                <?php while($p = $pedidos->fetch_assoc()): ?>
                    <option value="<?php echo $p['order_id']; ?>"
                        <?php if($p['order_id'] == $detalle['order_id']) echo 'selected'; ?>>
                        <?php echo $p['order_id']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Plato:
            <select name="menu_id" id="menu_select">
                <?php 
                mysqli_data_seek($menu, 0);
                while($m = $menu->fetch_assoc()): 
                ?>
                    <option value="<?php echo $m['menu_id']; ?>"
                        data-price="<?php echo $m['price']; ?>"
                        <?php if($m['menu_id'] == $detalle['menu_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($m['dish_name']); ?> (<?php echo $m['price']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <label>Cantidad:
            <input type="number" name="cantidad" value="<?php echo htmlspecialchars($detalle['quantity']); ?>">
        </label><br><br>

        <label>Precio Item:
            <input type="number" step="0.01" name="precio_item" id="precio_item" 
                   value="<?php echo htmlspecialchars($detalle['item_price']); ?>">
        </label><br><br>
        <button type="submit" class="actualizar">Guardar</button>
    </form>
    <script>
        document.querySelector("#menu_select").addEventListener("change", function(){
            let price = this.options[this.selectedIndex].getAttribute("data-price");
            document.querySelector("#precio_item").value = price;
        });
    </script>
    <?php
    return;
}

// LISTAR
$result = $mysqli->query("SELECT oi.*, rp.order_status, rm.dish_name
                          FROM restaurante_order_items oi
                          JOIN restaurante_pedidos rp ON oi.order_id = rp.order_id
                          JOIN restaurante_menu rm ON oi.menu_id = rm.menu_id
                          ORDER BY oi.order_item_id DESC");
?>
<h3>Detalles de Pedidos de Restaurante</h3>
<p><a href="?section=restaurante_pedidos_detalle&accion=crear" class="actualizar">+ Crear Detalle</a></p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Pedido</th>
            <th>Plato</th>
            <th>Cantidad</th>
            <th>Precio Item</th>
            <th>Estado Pedido</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($fila = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $fila['order_item_id']; ?></td>
            <td><?php echo $fila['order_id']; ?></td>
            <td><?php echo htmlspecialchars($fila['dish_name']); ?></td>
            <td><?php echo $fila['quantity']; ?></td>
            <td><?php echo $fila['item_price']; ?></td>
            <td><?php echo htmlspecialchars($fila['order_status']); ?></td>
            <td>
                <a href="?section=restaurante_pedidos_detalle&accion=editar&id=<?php echo $fila['order_item_id']; ?>" class="actualizar">✏</a>
                <a href="?section=restaurante_pedidos_detalle&accion=eliminar&id=<?php echo $fila['order_item_id']; ?>" class="eliminar"
                   onclick="return confirm('¿Eliminar este detalle de pedido?');">❌</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

