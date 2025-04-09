<?php
// super_controller.php
// This dynamic controller generates a CRUD interface for a specified table.

// Include your database configuration.
include "config.php";
$mysqli = new mysqli($host, $user, $pass, $dbName);
if ($mysqli->connect_errno) {
    die("Connection Error: " . $mysqli->connect_error);
}

// Get the table name from the URL (e.g. super_controller.php?table=mi_tabla)
$table = isset($_GET['table']) ? $mysqli->real_escape_string($_GET['table']) : '';
if (empty($table)) {
    die("No table specified.");
}

// Get the action: list, create, edit, or delete.
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';

// Fetch column metadata (including extra info) for the table.
$columns = [];
$columnsQuery = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_KEY 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME='$table'";
$result = $mysqli->query($columnsQuery);
if (!$result || $result->num_rows == 0) {
    die("Table not found or no columns available.");
}
while ($row = $result->fetch_assoc()) {
    $columns[] = $row;
}

// Determine the primary key column (assume single primary key for this example)
$primaryKey = null;
foreach ($columns as $col) {
    if ($col['COLUMN_KEY'] === 'PRI') {
        $primaryKey = $col['COLUMN_NAME'];
        break;
    }
}
if (!$primaryKey) {
    die("No primary key found for table '$table'.");
}

// Build foreign keys mapping (column => ['referenced_table'=>..., 'referenced_column'=>...])
$foreignKeys = [];
$fkQuery = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME='$table' AND REFERENCED_TABLE_NAME IS NOT NULL";
$resultFK = $mysqli->query($fkQuery);
while ($fk = $resultFK->fetch_assoc()) {
    $foreignKeys[$fk['COLUMN_NAME']] = [
        'referenced_table' => $fk['REFERENCED_TABLE_NAME'],
        'referenced_column' => $fk['REFERENCED_COLUMN_NAME']
    ];
}

// Helper function: maps SQL data types to HTML input types and bind types.
function getInputAttributes($dataType) {
    $dataType = strtolower($dataType);
    if (in_array($dataType, ['int', 'bigint', 'smallint', 'mediumint', 'tinyint'])) {
        return ['type' => 'number', 'bind' => 'i'];
    } elseif (in_array($dataType, ['decimal', 'numeric', 'float', 'double'])) {
        return ['type' => 'number', 'bind' => 'd', 'step' => '0.01'];
    } elseif (in_array($dataType, ['date'])) {
        return ['type' => 'date', 'bind' => 's'];
    } elseif (in_array($dataType, ['datetime', 'timestamp'])) {
        // Note: HTML5 datetime-local expects a "T" separator.
        return ['type' => 'datetime-local', 'bind' => 's'];
    } elseif (in_array($dataType, ['text', 'mediumtext', 'longtext'])) {
        return ['type' => 'textarea', 'bind' => 's'];
    } else {
        return ['type' => 'text', 'bind' => 's'];
    }
}

// Start HTML output.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dynamic CRUD: <?php echo htmlspecialchars($table); ?></title>
    <style>
        /* Basic styling */
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        form { margin-bottom: 20px; }
        label { display: block; margin: 5px 0; }
        input, textarea, select { width: 100%; padding: 5px; }
        .actions a { margin-right: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <h2>CRUD for table: <?php echo htmlspecialchars($table); ?></h2>
    <p><a href="?table=<?php echo urlencode($table); ?>&accion=crear">+ Crear nuevo registro</a> | <a href="?table=<?php echo urlencode($table); ?>&accion=listar">Listar registros</a></p>
    <hr>
<?php

// Switch among CRUD actions.
switch ($accion) {
    case 'listar':
        // List records.
        $query = "SELECT * FROM `$table` ORDER BY `$primaryKey` DESC";
        $result = $mysqli->query($query);
        if (!$result) {
            die("Error in query: " . $mysqli->error);
        }
        echo "<h3>Listado de registros</h3>";
        echo "<table>";
        echo "<thead><tr>";
        // Print header row.
        foreach ($columns as $col) {
            echo "<th>" . htmlspecialchars($col['COLUMN_NAME']) . "</th>";
        }
        echo "<th>Acciones</th>";
        echo "</tr></thead><tbody>";
        // Loop through rows.
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($columns as $col) {
                $val = isset($row[$col['COLUMN_NAME']]) ? $row[$col['COLUMN_NAME']] : "";
                echo "<td>" . htmlspecialchars($val) . "</td>";
            }
            // Action links for edit and delete.
            echo "<td class='actions'>";
            echo "<a href=\"?table=" . urlencode($table) . "&accion=editar&id=" . urlencode($row[$primaryKey]) . "\">✏ Editar</a>";
            echo "<a href=\"?table=" . urlencode($table) . "&accion=eliminar&id=" . urlencode($row[$primaryKey]) . "\" onclick=\"return confirm('¿Eliminar este registro?');\">❌ Eliminar</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        break;

    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form submission.
            $fields = [];
            $placeholders = [];
            $types = "";
            $values = [];
            foreach ($columns as $col) {
                // Skip auto-increment columns.
                if (strpos($col['EXTRA'], "auto_increment") !== false) continue;
                $colName = $col['COLUMN_NAME'];
                if (isset($_POST[$colName])) {
                    $fields[] = "`$colName`";
                    $placeholders[] = "?";
                    $attrs = getInputAttributes($col['DATA_TYPE']);
                    $types .= $attrs['bind'];
                    $values[] = $_POST[$colName];
                }
            }
            if (count($fields) > 0) {
                $sql = "INSERT INTO `$table` (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    die("Prepare failed: " . $mysqli->error);
                }
                // Use call_user_func_array to bind parameters.
                $bind_names[] = $types;
                for ($i = 0; $i < count($values); $i++) {
                    $bind_names[] = &$values[$i];
                }
                call_user_func_array(array($stmt, 'bind_param'), $bind_names);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
                $stmt->close();
            }
            header("Location: ?table=" . urlencode($table) . "&accion=listar");
            exit;
        } else {
            // Display the create form.
            echo "<h3>Crear Nuevo Registro</h3>";
            echo "<form method='post' action='?table=" . urlencode($table) . "&accion=crear'>";
            // For each column, generate an input (skip auto-increment fields).
            foreach ($columns as $col) {
                if (strpos($col['EXTRA'], "auto_increment") !== false) continue;
                $colName = $col['COLUMN_NAME'];
                $dataType = $col['DATA_TYPE'];
                $attrs = getInputAttributes($dataType);
                echo "<label>" . htmlspecialchars($colName) . ":";
                // If this column is a foreign key, generate a select box.
                if (isset($foreignKeys[$colName])) {
                    $ref = $foreignKeys[$colName];
                    $refTable = $ref['referenced_table'];
                    $refColumn = $ref['referenced_column'];
                    // Try to pick a display column (first non-PK column) from referenced table.
                    $dispColQuery = "SELECT COLUMN_NAME 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME='$refTable' AND COLUMN_KEY <> 'PRI'
                        ORDER BY ORDINAL_POSITION LIMIT 1";
                    $dispColRes = $mysqli->query($dispColQuery);
                    if ($dispColRes && $dispRow = $dispColRes->fetch_assoc()) {
                        $displayColumn = $dispRow['COLUMN_NAME'];
                    } else {
                        $displayColumn = $refColumn;
                    }
                    // Query the referenced table.
                    $fkQuery = "SELECT `$refColumn` as ref_val, `$displayColumn` as ref_disp FROM `$refTable`";
                    $fkResult = $mysqli->query($fkQuery);
                    echo "<select name='" . htmlspecialchars($colName) . "'>";
                    while ($fkRow = $fkResult->fetch_assoc()) {
                        echo "<option value=\"" . htmlspecialchars($fkRow['ref_val']) . "\">" . htmlspecialchars($fkRow['ref_disp']) . "</option>";
                    }
                    echo "</select>";
                } else {
                    // For textarea types.
                    if ($attrs['type'] === 'textarea') {
                        echo "<textarea name='" . htmlspecialchars($colName) . "'></textarea>";
                    } else {
                        echo "<input type='" . htmlspecialchars($attrs['type']) . "' name='" . htmlspecialchars($colName) . "'";
                        if (isset($attrs['step'])) {
                            echo " step='" . htmlspecialchars($attrs['step']) . "'";
                        }
                        echo ">";
                    }
                }
                echo "</label>";
            }
            echo "<br><button type='submit'>Crear</button>";
            echo "</form>";
        }
        break;

    case 'editar':
        // Requires the primary key value, passed as id parameter.
        if (!isset($_GET['id'])) {
            die("No ID provided for edit.");
        }
        $id = $mysqli->real_escape_string($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process update form.
            $setClauses = [];
            $types = "";
            $values = [];
            foreach ($columns as $col) {
                // Skip auto-increment primary key.
                if ($col['COLUMN_NAME'] === $primaryKey && strpos($col['EXTRA'], "auto_increment") !== false) continue;
                $colName = $col['COLUMN_NAME'];
                if (isset($_POST[$colName])) {
                    $setClauses[] = "`$colName` = ?";
                    $attrs = getInputAttributes($col['DATA_TYPE']);
                    $types .= $attrs['bind'];
                    $values[] = $_POST[$colName];
                }
            }
            // Append the id for the WHERE clause.
            $types .= "i";
            $values[] = $id;
            $sql = "UPDATE `$table` SET " . implode(", ", $setClauses) . " WHERE `$primaryKey` = ?";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $mysqli->error);
            }
            $bind_names[] = $types;
            for ($i = 0; $i < count($values); $i++) {
                $bind_names[] = &$values[$i];
            }
            call_user_func_array(array($stmt, 'bind_param'), $bind_names);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            $stmt->close();
            header("Location: ?table=" . urlencode($table) . "&accion=listar");
            exit;
        } else {
            // Display the edit form.
            $query = "SELECT * FROM `$table` WHERE `$primaryKey` = '$id' LIMIT 1";
            $result = $mysqli->query($query);
            if (!$result || $result->num_rows == 0) {
                die("Record not found.");
            }
            $record = $result->fetch_assoc();
            echo "<h3>Editar Registro (ID: " . htmlspecialchars($id) . ")</h3>";
            echo "<form method='post' action='?table=" . urlencode($table) . "&accion=editar&id=" . urlencode($id) . "'>";
            foreach ($columns as $col) {
                // Optionally, skip auto_increment primary key from being edited.
                if ($col['COLUMN_NAME'] === $primaryKey && strpos($col['EXTRA'], "auto_increment") !== false) {
                    echo "<p><strong>" . htmlspecialchars($col['COLUMN_NAME']) . ":</strong> " . htmlspecialchars($record[$col['COLUMN_NAME']]) . "</p>";
                    continue;
                }
                $colName = $col['COLUMN_NAME'];
                $attrs = getInputAttributes($col['DATA_TYPE']);
                $value = isset($record[$colName]) ? $record[$colName] : "";
                // For datetime-local, convert value (if needed) replacing space with "T"
                if ($attrs['type'] === 'datetime-local') {
                    $value = str_replace(' ', 'T', $value);
                }
                echo "<label>" . htmlspecialchars($colName) . ":";
                if (isset($foreignKeys[$colName])) {
                    $ref = $foreignKeys[$colName];
                    $refTable = $ref['referenced_table'];
                    $refColumn = $ref['referenced_column'];
                    $dispColQuery = "SELECT COLUMN_NAME 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME='$refTable' AND COLUMN_KEY <> 'PRI'
                        ORDER BY ORDINAL_POSITION LIMIT 1";
                    $dispColRes = $mysqli->query($dispColQuery);
                    if ($dispColRes && $dispRow = $dispColRes->fetch_assoc()) {
                        $displayColumn = $dispRow['COLUMN_NAME'];
                    } else {
                        $displayColumn = $refColumn;
                    }
                    $fkQuery = "SELECT `$refColumn` as ref_val, `$displayColumn` as ref_disp FROM `$refTable`";
                    $fkResult = $mysqli->query($fkQuery);
                    echo "<select name='" . htmlspecialchars($colName) . "'>";
                    while ($fkRow = $fkResult->fetch_assoc()) {
                        $selected = ($fkRow['ref_val'] == $value) ? "selected" : "";
                        echo "<option value=\"" . htmlspecialchars($fkRow['ref_val']) . "\" $selected>" . htmlspecialchars($fkRow['ref_disp']) . "</option>";
                    }
                    echo "</select>";
                } else {
                    if ($attrs['type'] === 'textarea') {
                        echo "<textarea name='" . htmlspecialchars($colName) . "'>" . htmlspecialchars($value) . "</textarea>";
                    } else {
                        echo "<input type='" . htmlspecialchars($attrs['type']) . "' name='" . htmlspecialchars($colName) . "' value=\"" . htmlspecialchars($value) . "\"";
                        if (isset($attrs['step'])) {
                            echo " step='" . htmlspecialchars($attrs['step']) . "'";
                        }
                        echo ">";
                    }
                }
                echo "</label>";
            }
            echo "<br><button type='submit'>Guardar Cambios</button>";
            echo "</form>";
        }
        break;

    case 'eliminar':
        if (!isset($_GET['id'])) {
            die("No ID provided for deletion.");
        }
        $id = $mysqli->real_escape_string($_GET['id']);
        $sql = "DELETE FROM `$table` WHERE `$primaryKey` = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            die("Deletion failed: " . $stmt->error);
        }
        $stmt->close();
        header("Location: ?table=" . urlencode($table) . "&accion=listar");
        exit;
        break;

    default:
        echo "<p>Acción no reconocida.</p>";
        break;
}

$mysqli->close();
?>
</body>
</html>

