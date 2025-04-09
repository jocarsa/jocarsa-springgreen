<?php
// super_controlador.php
// Dynamic CRUD controller with on‑the‑fly creation of foreign key records,
// including pre‑filling insert fields from URL parameters.
// All actions (listar, crear, editar, eliminar, crear_foreign) are handled here.
include "config.php";

$mysqli = new mysqli($host, $user, $pass, $dbName);
if ($mysqli->connect_errno) {
    die("Connection Error: " . $mysqli->connect_error);
}

// Get the main table name from URL, e.g. ?table=mi_tabla
$table = isset($_GET['table']) ? $mysqli->real_escape_string($_GET['table']) : '';
if (empty($table)) {
    die("No table specified.");
}

// Get the action (listar, crear, editar, eliminar, or crear_foreign)
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';

// Fetch table comment (optional, for display)
$tableComment = "";
$tableCommentQuery = "SELECT TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME='$table'";
$resultTableComment = $mysqli->query($tableCommentQuery);
if ($resultTableComment && $rowTable = $resultTableComment->fetch_assoc()) {
    $tableComment = $rowTable['TABLE_COMMENT'];
}

// Fetch column metadata for the main table.
$columns = [];
$columnsQuery = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_KEY, COLUMN_COMMENT 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME='$table'";
$result = $mysqli->query($columnsQuery);
if (!$result || $result->num_rows == 0) {
    die("Table not found or no columns available.");
}
while ($row = $result->fetch_assoc()) {
    $columns[] = $row;
}

// Determine the primary key (assuming single primary key)
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

// Build foreign keys mapping (each foreign key: column => [referenced_table, referenced_column])
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

// Helper function: map SQL data types to appropriate HTML input types, bind types and additional attributes.
function getInputAttributes($dataType) {
    $dataType = strtolower($dataType);
    if (in_array($dataType, ['int', 'bigint', 'smallint', 'mediumint', 'tinyint'])) {
        return ['type' => 'number', 'bind' => 'i'];
    } elseif (in_array($dataType, ['decimal', 'numeric', 'float', 'double'])) {
        return ['type' => 'number', 'bind' => 'd', 'step' => '0.01'];
    } elseif (in_array($dataType, ['date'])) {
        return ['type' => 'date', 'bind' => 's'];
    } elseif (in_array($dataType, ['datetime', 'timestamp'])) {
        // HTML5 requires a "T" separator for datetime-local
        return ['type' => 'datetime-local', 'bind' => 's'];
    } elseif (in_array($dataType, ['text', 'mediumtext', 'longtext'])) {
        return ['type' => 'textarea', 'bind' => 's'];
    } else {
        return ['type' => 'text', 'bind' => 's'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dynamic CRUD: <?php echo htmlspecialchars($table); ?></title>
  <style>
    /* Basic styling for forms and tables */
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #eee; }
    form { margin-bottom: 20px; }
    label { display: block; margin: 5px 0; }
    input, textarea, select { width: 100%; padding: 5px; box-sizing: border-box; }
    .actions a { margin-right: 5px; text-decoration: none; }
    .column-comment { font-size: 0.85em; color: #666; }
    .crud-form fieldset { margin-bottom: 10px; }
  </style>
</head>
<body>
  <h2>CRUD for table: <?php echo htmlspecialchars($table); ?></h2>
  <?php if (!empty($tableComment)) { echo "<p><em>" . htmlspecialchars($tableComment) . "</em></p>"; } ?>
  <p>
    <a href="?table=<?php echo urlencode($table); ?>&accion=crear">+ Crear nuevo registro</a> | 
    <a href="?table=<?php echo urlencode($table); ?>&accion=listar">Listar registros</a>
  </p>
  <hr>
<?php
switch ($accion) {

    // ------------------------------
    // List all records
    case 'listar':
        $query = "SELECT * FROM `$table` ORDER BY `$primaryKey` DESC";
        $result = $mysqli->query($query);
        if (!$result) {
            die("Error in query: " . $mysqli->error);
        }
        echo "<h3>Listado de registros</h3>";
        echo "<table>";
        echo "<thead><tr>";
        foreach ($columns as $col) {
            echo "<th>" . htmlspecialchars($col['COLUMN_NAME']) . "</th>";
        }
        echo "<th>Acciones</th>";
        echo "</tr></thead><tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($columns as $col) {
                $val = isset($row[$col['COLUMN_NAME']]) ? $row[$col['COLUMN_NAME']] : "";
                echo "<td>" . htmlspecialchars($val) . "</td>";
            }
            echo "<td class='actions'>";
            echo "<a href=\"?table=" . urlencode($table) . "&accion=editar&id=" . urlencode($row[$primaryKey]) . "\">✏ Editar</a>";
            echo "<a href=\"?table=" . urlencode($table) . "&accion=eliminar&id=" . urlencode($row[$primaryKey]) . "\" onclick=\"return confirm('¿Eliminar este registro?');\">❌ Eliminar</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        break;

    // ------------------------------
    // Create new main record (Form A)
    case 'crear':
        // Process POST to insert new record
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Save current form data in session (to recover in case of redirection)
            $_SESSION['formA_data'] = $_POST;
            $fields = [];
            $placeholders = [];
            $types = "";
            $values = [];
            foreach ($columns as $col) {
                if (strpos($col['EXTRA'], "auto_increment") !== false) continue;
                $colName = $col['COLUMN_NAME'];

                // --- Restoring default value: order of precedence is:
                // GET parameter with field name > session data > (if foreign creation) new_field value.
                $defaultValue = "";
                if (isset($_GET[$colName])) {
                    $defaultValue = $_GET[$colName];
                }
                if (isset($_SESSION['formA_data'][$colName])) {
                    $defaultValue = $_SESSION['formA_data'][$colName];
                }
                if (isset($_GET['new_' . $colName])) {
                    $defaultValue = $_GET['new_' . $colName];
                }
                
                $attrs = getInputAttributes($col['DATA_TYPE']);
                // For date/datetime-local fields, if not provided, default to now.
                if (($attrs['type'] === 'date' || $attrs['type'] === 'datetime-local') && trim($defaultValue) === '') {
						 $defaultValue = ($attrs['type'] === 'date') ? date('Y-m-d') : date('Y-m-d\TH:i');
					}
                
                $fields[] = "`$colName`";
                $placeholders[] = "?";
                $types .= $attrs['bind'];
                $values[] = $defaultValue;
            }
            if (count($fields) > 0) {
                $sql = "INSERT INTO `$table` (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    die("Prepare failed: " . $mysqli->error);
                }
                $bind_names = [];
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
            unset($_SESSION['formA_data']); // Clear saved form state on success
            echo '<script>window.location = "?table=' . urlencode($table) . '&accion=listar"</script>';
            exit;
        } else {
            // Display the creation form for main record (Form A)
            $savedData = isset($_SESSION['formA_data']) ? $_SESSION['formA_data'] : [];
            echo "<h3>Crear Nuevo Registro</h3>";
            echo "<form method='post' action='?table=" . urlencode($table) . "&accion=crear' class='crud-form'>";
            foreach ($columns as $col) {
                if (strpos($col['EXTRA'], "auto_increment") !== false) continue;
                echo "<fieldset>";
                $colName = $col['COLUMN_NAME'];
                $attrs = getInputAttributes($col['DATA_TYPE']);
                
                // Pre-fill default value from GET (if provided), then session, then check if a new FK record was created.
                $defaultValue = "";
                if (isset($_GET[$colName])) {
                    $defaultValue = $_GET[$colName];
                }
                if (isset($savedData[$colName])) {
                    $defaultValue = $savedData[$colName];
                }
                if (isset($_GET['new_' . $colName])) {
                    $defaultValue = $_GET['new_' . $colName];
                }
                
                echo "<label>" . htmlspecialchars($colName) . ":";
                if (!empty($col['COLUMN_COMMENT'])) {
                    echo "<br><span class='column-comment'>" . htmlspecialchars($col['COLUMN_COMMENT']) . "</span>";
                }
                echo "</label>";
                
                // If this column is a foreign key, render a select element with an "Add New" link.
                if (isset($foreignKeys[$colName])) {
                    $ref = $foreignKeys[$colName];
                    $refTable = $ref['referenced_table'];
                    $refColumn = $ref['referenced_column'];
                    $fkQuery = "SELECT * FROM `$refTable`";
                    $fkResult = $mysqli->query($fkQuery);
                    echo "<select name='" . htmlspecialchars($colName) . "'>";
                    while ($fkRow = $fkResult->fetch_assoc()) {
                        $optionValue = $fkRow[$refColumn];
                        $optionText = implode(" - ", array_map('htmlspecialchars', $fkRow));
                        $selected = ($defaultValue !== "" && $defaultValue == $optionValue) ? "selected" : "";
                        echo "<option value=\"" . htmlspecialchars($optionValue) . "\" $selected>" . $optionText . "</option>";
                    }
                    echo "</select>";
                    
                    // Prepare return URL so that after creating a new FK record the user returns here.
                    $currentUrl = $_SERVER['REQUEST_URI'];
                    $encodedUrl = urlencode($currentUrl);
                    echo ' <a href="?table=' . urlencode($table) .
                         '&accion=crear_foreign'
                         . '&foreign_field=' . urlencode($colName)
                         . '&foreign_table=' . urlencode($refTable)
                         . '&return_url=' . $encodedUrl
                         . '">+</a>';
                } else {
                    // Render a normal input field with appropriate type.
                    if ($attrs['type'] === 'textarea') {
                        echo "<textarea name='" . htmlspecialchars($colName) . "'>" . htmlspecialchars($defaultValue) . "</textarea>";
                    } else {
                        echo "<input type='" . htmlspecialchars($attrs['type']) . "' name='" . htmlspecialchars($colName) . "' value='" . htmlspecialchars($defaultValue) . "'";
                        if (isset($attrs['step'])) {
                            echo " step='" . htmlspecialchars($attrs['step']) . "'";
                        }
                        echo ">";
                    }
                }
                echo "</fieldset>";
            }
            echo "<br><button type='submit'>Crear</button>";
            echo "</form>";
        }
        break;
        
    // ------------------------------
    // Edit existing record
    case 'editar':
        if (!isset($_GET['id'])) {
            die("No ID provided for edit.");
        }
        $id = $mysqli->real_escape_string($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $setClauses = [];
            $types = "";
            $values = [];
            foreach ($columns as $col) {
                if ($col['COLUMN_NAME'] === $primaryKey && strpos($col['EXTRA'], "auto_increment") !== false) continue;
                $colName = $col['COLUMN_NAME'];
                if (isset($_POST[$colName])) {
                    $setClauses[] = "`$colName` = ?";
                    $attrs = getInputAttributes($col['DATA_TYPE']);
                    $types .= $attrs['bind'];
                    $values[] = $_POST[$colName];
                }
            }
            $types .= "i";
            $values[] = $id;
            $sql = "UPDATE `$table` SET " . implode(", ", $setClauses) . " WHERE `$primaryKey` = ?";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $mysqli->error);
            }
            $bind_names = [];
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
            $query = "SELECT * FROM `$table` WHERE `$primaryKey` = '$id' LIMIT 1";
            $result = $mysqli->query($query);
            if (!$result || $result->num_rows == 0) {
                die("Record not found.");
            }
            $record = $result->fetch_assoc();
            echo "<h3>Editar Registro (ID: " . htmlspecialchars($id) . ")</h3>";
            echo "<form method='post' action='?table=" . urlencode($table) . "&accion=editar&id=" . urlencode($id) . "' class='crud-form'>";
            foreach ($columns as $col) {
                if ($col['COLUMN_NAME'] === $primaryKey && strpos($col['EXTRA'], "auto_increment") !== false) {
                    echo "<fieldset>";
                    echo "<p><strong>" . htmlspecialchars($col['COLUMN_NAME']) . ":</strong> " . htmlspecialchars($record[$col['COLUMN_NAME']]) . "</p>";
                    echo "</fieldset>";
                    continue;
                }
                echo "<fieldset>";
                $colName = $col['COLUMN_NAME'];
                $attrs = getInputAttributes($col['DATA_TYPE']);
                $value = isset($record[$colName]) ? $record[$colName] : "";
                if ($attrs['type'] === 'datetime-local') {
                    $value = str_replace(' ', 'T', $value);
                }
                echo "<label>" . htmlspecialchars($colName) . ":";
                if (!empty($col['COLUMN_COMMENT'])) {
                    echo "<br><span class='column-comment'>" . htmlspecialchars($col['COLUMN_COMMENT']) . "</span>";
                }
                echo "</label>";
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
                echo "</fieldset>";
            }
            echo "<br><button type='submit'>Guardar Cambios</button>";
            echo "</form>";
        }
        break;
        
    // ------------------------------
    // Delete record
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
        echo '<script>window.location = "?table=' . urlencode($table) . '&accion=listar"</script>';
        exit;
        break;
        
    // ------------------------------
    // Create new foreign record (Form B)
    // This branch dynamically generates a creation form for the foreign table,
    // using the same style, input types and comments as in the main form.
    // It accepts three GET parameters: foreign_field (the target field in form A),
    // foreign_table (the table in which to create the new record) and return_url (the URL to return to).
    case 'crear_foreign':
        $foreign_field = isset($_GET['foreign_field']) ? $mysqli->real_escape_string($_GET['foreign_field']) : '';
        $foreign_table = isset($_GET['foreign_table']) ? $mysqli->real_escape_string($_GET['foreign_table']) : '';
        $return_url    = isset($_GET['return_url']) ? urldecode($_GET['return_url']) : '';
        
        if (empty($foreign_field) || empty($foreign_table) || empty($return_url)) {
            die("Faltan parámetros necesarios para crear el registro foráneo.");
        }
        
        // Retrieve column metadata for the foreign table.
        $fkColumns = [];
        $columnsQuery = "SELECT COLUMN_NAME, DATA_TYPE, EXTRA, COLUMN_COMMENT 
                         FROM INFORMATION_SCHEMA.COLUMNS 
                         WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME='$foreign_table'";
        $resultFkCols = $mysqli->query($columnsQuery);
        if (!$resultFkCols || $resultFkCols->num_rows == 0) {
            die("Tabla foránea no encontrada o sin columnas.");
        }
        while ($row = $resultFkCols->fetch_assoc()) {
            $fkColumns[] = $row;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fields = [];
            $placeholders = [];
            $types = "";
            $values = [];
            foreach ($fkColumns as $col) {
                if (strpos($col['EXTRA'], "auto_increment") !== false) continue;
                $colName = $col['COLUMN_NAME'];
                // Allow URL parameters to pre-fill foreign form as well.
                $fkDefault = "";
                if (isset($_GET[$colName])) {
                    $fkDefault = $_GET[$colName];
                }
                if (isset($_POST[$colName])) {
                    $fkDefault = $_POST[$colName];
                }
                $fields[] = "`$colName`";
                $placeholders[] = "?";
                $attrs = getInputAttributes($col['DATA_TYPE']);
                $types .= $attrs['bind'];
                $values[] = $fkDefault;
            }
            if (count($fields) > 0) {
                $sql = "INSERT INTO `$foreign_table` (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    die("Prepare failed: " . $mysqli->error);
                }
                $bind_names = [];
                $bind_names[] = $types;
                for ($i = 0; $i < count($values); $i++) {
                    $bind_names[] = &$values[$i];
                }
                call_user_func_array(array($stmt, 'bind_param'), $bind_names);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
                $newId = $stmt->insert_id;
                $stmt->close();
                
                // Append the new foreign record id as a parameter (e.g. new_<foreign_field>=<newId>) to the return URL.
                $param = "new_" . urlencode($foreign_field) . "=" . urlencode($newId);
                if (strpos($return_url, '?') !== false) {
                    $return_url .= "&" . $param;
                } else {
                    $return_url .= "?" . $param;
                }
                echo '<script>window.location = "'.$return_url.'"</script>';
                echo $return_url;
                //header("Location: " . $return_url);
                exit;
            }
        } else {
            // Display form for creating a new record in the foreign table (Form B).
            echo "<h2>Crear nuevo registro en " . htmlspecialchars($foreign_table) . "</h2>";
            echo "<form method='post' action='?table=" . urlencode($table) 
                 . "&accion=crear_foreign"
                 . "&foreign_field=" . urlencode($foreign_field)
                 . "&foreign_table=" . urlencode($foreign_table)
                 . "&return_url=" . urlencode($return_url)
                 . "' class='crud-form'>";
            foreach ($fkColumns as $col) {
                if (strpos($col['EXTRA'], "auto_increment") !== false) continue;
                echo "<fieldset>";
                $colName = $col['COLUMN_NAME'];
                $attrs = getInputAttributes($col['DATA_TYPE']);
                // Pre-fill using GET if available.
                $fkDefault = "";
                if (isset($_GET[$colName])) {
                    $fkDefault = $_GET[$colName];
                }
                echo "<label>" . htmlspecialchars($colName) . ":";
                if (!empty($col['COLUMN_COMMENT'])) {
                    echo "<br><span class='column-comment'>" . htmlspecialchars($col['COLUMN_COMMENT']) . "</span>";
                }
                echo "</label>";
                if ($attrs['type'] === 'textarea') {
                    echo "<textarea name='" . htmlspecialchars($colName) . "'>" . htmlspecialchars($fkDefault) . "</textarea>";
                } else {
                    echo "<input type='" . htmlspecialchars($attrs['type']) . "' name='" . htmlspecialchars($colName) . "' value='" . htmlspecialchars($fkDefault) . "'";
                    if (isset($attrs['step'])) {
                        echo " step='" . htmlspecialchars($attrs['step']) . "'";
                    }
                    echo " required>";
                }
                echo "</fieldset>";
            }
            echo "<button type='submit'>Crear registro</button>";
            echo "</form>";
            echo '<p><a href="' . htmlspecialchars($return_url) . '">Cancelar y volver</a></p>';
        }
        break;
        
    default:
        echo "<p>Acción no reconocida.</p>";
        break;
}
$mysqli->close();
?>
</body>
</html>

