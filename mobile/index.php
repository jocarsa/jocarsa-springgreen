<?php
/**
 * reservation.php
 *
 * End‑user reservation webapp (mobile style, plain PHP).
 * Steps:
 *  1. Show a form to enter arrival and departure dates.
 *  2. On submission, check room availability.
 *  3. If available, show a form to choose a room and enter guest data.
 *  4. Insert guest into "huespedes" (using column "apellidos" for the surname) and create a reservation record in "reservas".
 *  5. Display a thank you message.
 */

// Database connection details (as in your hotel management system)
$host   = 'localhost';
$user   = 'springgreen';
$pass   = 'springgreen';
$dbName = 'springgreen';

// Create MySQL connection.
$mysqli = new mysqli($host, $user, $pass, $dbName);
if ($mysqli->connect_errno) {
    die("MySQL Connection Error: " . $mysqli->connect_error);
}

// Determine the current step based on a hidden field "step" (empty means first step)
$step = isset($_POST['step']) ? $_POST['step'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reserva de Habitación</title>
  <!-- Link to the mobile CSS so the visual style is maintained -->
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div id="appContainer" class="screen">
    <!-- Header using mobile styling -->
    <header id="appHeader">
      <h1>Reserva de Habitación</h1>
    </header>

    <!-- Main screen where content will be displayed -->
    <main id="mainScreen">
      <?php
      // STEP 1: Show the room availability check form.
      if ($step === '') {
          ?>
          <form method="post">
            <h2>Verificar Disponibilidad</h2>
            <label for="arrival_date">Fecha de Llegada:</label>
            <input type="date" id="arrival_date" name="arrival_date" required value="<?php echo date('Y-m-d'); ?>">
            
            <label for="departure_date">Fecha de Salida:</label>
            <input type="date" id="departure_date" name="departure_date" required value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            
            <!-- Hidden field to mark the next step -->
            <input type="hidden" name="step" value="check_availability">
            <button type="submit">Comprobar Disponibilidad</button>
          </form>
          <?php
      }
      // STEP 2: Process availability check and show available room(s) plus a form for guest data.
      elseif ($step === 'check_availability') {
          // Retrieve the posted dates.
          $arrival_date   = $_POST['arrival_date'];
          $departure_date = $_POST['departure_date'];

          // Query to fetch all rooms from the "habitaciones" table.
          $rooms = [];
          $sqlRooms = "SELECT * FROM habitaciones ORDER BY numero ASC";
          if ($resultRooms = $mysqli->query($sqlRooms)) {
              while ($room = $resultRooms->fetch_assoc()) {
                  $rooms[] = $room;
              }
              $resultRooms->free();
          }

          // Get reservations overlapping the selected dates.
          // We assume reservations use columns fecha_checkin and fecha_checkout.
          $reservations = [];
          $sqlRes = "SELECT * FROM reservas WHERE fecha_checkin < '$departure_date' AND fecha_checkout > '$arrival_date'";
          if ($resultRes = $mysqli->query($sqlRes)) {
              while ($res = $resultRes->fetch_assoc()) {
                  $reservations[] = $res;
              }
              $resultRes->free();
          }

          // Build a list of room IDs that are already reserved in this period.
          $unavailableRoomIds = [];
          foreach ($reservations as $res) {
              $unavailableRoomIds[] = $res['habitacion_id'];
          }

          // Determine available rooms.
          $availableRooms = [];
          foreach ($rooms as $room) {
              if (!in_array($room['habitacion_id'], $unavailableRoomIds)) {
                  $availableRooms[] = $room;
              }
          }

          // If no rooms are available, let the user know.
          if (count($availableRooms) === 0) {
              echo "<p>Lo sentimos, no hay habitaciones disponibles para las fechas seleccionadas.</p>";
              echo "<p><a href='reservation.php'>Volver a Intentar</a></p>";
          } else {
              // Otherwise, show a form with a dropdown of available rooms and ask for guest data.
              ?>
              <h2>Habitaciones Disponibles</h2>
              <p>Seleccione una habitación y complete sus datos para reservar.</p>
              <form method="post">
                  <label for="room_id">Habitación:</label>
                  <select name="room_id" id="room_id" required>
                      <?php
                      // List available rooms. Se asume que "numero" contiene el número o identificador visible.
                      foreach ($availableRooms as $room) {
                          $displayText = htmlspecialchars($room['numero']);
                          if (isset($room['descripcion']) && !empty($room['descripcion'])) {
                              $displayText .= " - " . htmlspecialchars($room['descripcion']);
                          }
                          echo "<option value='" . htmlspecialchars($room['habitacion_id']) . "'>" . $displayText . "</option>";
                      }
                      ?>
                  </select>

                  <!-- Guest data fields -->
                  <label for="nombre">Nombre:</label>
                  <input type="text" name="nombre" id="nombre" required>

                  <label for="apellido">Apellidos:</label>
                  <!-- Note: The form field is still labeled "apellido" for user input, but it will be mapped to the "apellidos" column -->
                  <input type="text" name="apellido" id="apellido" required>

                  <label for="email">Email:</label>
                  <input type="email" name="email" id="email" required>

                  <label for="telefono">Teléfono:</label>
                  <input type="text" name="telefono" id="telefono">

                  <!-- Pass along the dates using hidden fields -->
                  <input type="hidden" name="arrival_date" value="<?php echo htmlspecialchars($arrival_date); ?>">
                  <input type="hidden" name="departure_date" value="<?php echo htmlspecialchars($departure_date); ?>">
                  <input type="hidden" name="step" value="make_reservation">

                  <button type="submit">Reservar</button>
              </form>
              <?php
          }
      }
      // STEP 3: Process the reservation by inserting guest and reservation records in the database.
      elseif ($step === 'make_reservation') {
          // Retrieve posted data.
          $arrival_date   = $_POST['arrival_date'];
          $departure_date = $_POST['departure_date'];
          $room_id        = $_POST['room_id'];
          $nombre         = $mysqli->real_escape_string($_POST['nombre']);
          // Map the form input "apellido" to the variable $apellidos.
          $apellidos      = $mysqli->real_escape_string($_POST['apellido']);
          $email          = $mysqli->real_escape_string($_POST['email']);
          $telefono       = $mysqli->real_escape_string($_POST['telefono']);

          // Insert a new guest into the "huespedes" table using the column "apellidos" for the surname.
          $sqlInsertHuespedes = "INSERT INTO huespedes (nombre, apellidos, email, telefono)
                                 VALUES ('$nombre', '$apellidos', '$email', '$telefono')";
          if (!$mysqli->query($sqlInsertHuespedes)) {
              echo "<p>Error al crear el huésped: " . $mysqli->error . "</p>";
          } else {
              // Get the new guest’s ID.
              $huesped_id = $mysqli->insert_id;

              // Insert the reservation into the "reservas" table.
              // We assume that the table "reservas" has the fields:
              // huesped_id, habitacion_id, fecha_checkin, fecha_checkout.
    $sqlInsertReserva = "INSERT INTO reservas (huesped_id, habitacion_id, propiedad_id, fecha_checkin, fecha_checkout)
                     VALUES ($huesped_id, $room_id, 1, '$arrival_date', '$departure_date')";

              if (!$mysqli->query($sqlInsertReserva)) {
                  echo "<p>Error al crear la reserva: " . $mysqli->error . "</p>";
              } else {
                  ?>
                  <h2>¡Gracias!</h2>
                  <p>Su reserva se ha realizado exitosamente.</p>
                  <p><a href="?">Hacer otra reserva</a></p>
                  <?php
              }
          }
      }
      ?>
    </main>

    <!-- Optionally, you can add a footer here -->
    <footer>
      <!-- For example, a simple footer or navigation -->
    </footer>
  </div>
</body>
</html>

<?php
// Always close the database connection.
$mysqli->close();
?>

