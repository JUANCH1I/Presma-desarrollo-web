<?php
include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$config = include '../db.php';
$conexion = conexion();
$statement = $conexion->prepare("SELECT * FROM rol");
$statement->execute();
$datos = $statement->fetchAll();

$resultado = [
  'error' => false,
  'mensaje' => ''
];

if (!isset($_GET['id'])) {
  $resultado['error'] = true;
  $resultado['mensaje'] = 'El alumno no existe';
}

if (isset($_POST['submit'])) {
  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $alumno = [
      "id"        => $_GET['id'],
      "nombre"    => $_POST['nombre'],
      "email"     => $_POST['email'],
      "rol"      => $_POST['rol'],
    ];

    $consultaSQL = "UPDATE users SET
            user_name = :nombre,
            user_email = :email,
            idRol = :rol
            WHERE user_id = :id";
    $consulta = $conexion->prepare($consultaSQL);
    $consulta->execute($alumno);

    // Limpiar las asignaciones anteriores
    $limpiarAsignacionesSQL = "DELETE FROM AsignacionEtiquetas WHERE UsuarioID = :id";
    $limpiarAsignaciones = $conexion->prepare($limpiarAsignacionesSQL);
    $limpiarAsignaciones->execute(['id' => $_GET['id']]);

    // Asignar nuevas etiquetas
    if (isset($_POST['etiquetas']) && is_array($_POST['etiquetas'])) {
      $insertarEtiquetasSQL = "INSERT INTO AsignacionEtiquetas (UsuarioID, EtiquetaID) VALUES (:id, :etiqueta)";
      $insertarEtiquetas = $conexion->prepare($insertarEtiquetasSQL);

      foreach ($_POST['etiquetas'] as $etiqueta) {
        $insertarEtiquetas->execute(['id' => $_GET['id'], 'etiqueta' => $etiqueta]);
      }
    }
  } catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

  $id = $_GET['id'];
  $consultaSQL = "SELECT * FROM users WHERE user_id =" . $id;

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $alumno = $sentencia->fetch(PDO::FETCH_ASSOC);

  if (!$alumno) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'No se ha encontrado el alumno';
  }

  // Obtener etiquetas asignadas al usuario
  $etiquetasAsignadasSQL = "SELECT EtiquetaID FROM AsignacionEtiquetas WHERE UsuarioID = :id";
  $etiquetasAsignadas = $conexion->prepare($etiquetasAsignadasSQL);
  $etiquetasAsignadas->execute(['id' => $id]);
  $etiquetasAsignadas = $etiquetasAsignadas->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $error) {
  $resultado['error'] = true;
  $resultado['mensaje'] = $error->getMessage();
}


// Obtener todas las etiquetas disponibles
$etiquetasDisponiblesSQL = "SELECT * FROM Etiquetas";
$etiquetasDisponibles = $conexion->query($etiquetasDisponiblesSQL)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require "../template/header.php"; ?>

<?php
if ($resultado['error']) {
?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
<?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          El alumno ha sido actualizado correctamente
        </div>
      </div>
    </div>
  </div>
<?php
}
?>

<?php
if (isset($alumno) && $alumno) {
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando el alumno <?= escapar($alumno['user_name']) . ' ' ?></h2>
        <hr>
        <form method="post">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?= escapar($alumno['user_name']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= escapar($alumno['user_email']) ?>" class="form-control">
          </div>
          <br>
          <div class="form-group">
            <label for="rol">Rol</label>
            <select name="rol" id="rol" class="input">
              <?php foreach ($datos as $dato) : ?>
                <option value="<?= $dato['idRol'] ?>" class="input" <?= ($alumno['idRol'] == $dato['idRol']) ? 'selected' : '' ?>>
                  <?= $dato['rol_descripcion'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          

          
          <div class="form-group">
            <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
            <input type="submit" name="submit" class="btn btn-primary" value="Actualizar">
            <a class="btn btn-primary" href="abmPersonas.php">Regresar</a>
          </div>
        </form>
        <div>
            <h2>Etiquetas asignadas</h2>
            <select id="etiquetasAsignadas" multiple>
              <!-- Etiquetas asignadas al alumno -->
            </select>
            <button onclick="eliminarEtiqueta()">Eliminar</button>
          </div>
          <div>
            <h2>Etiquetas disponibles</h2>
            <select id="etiquetasDisponibles" multiple>
              <!-- Opciones de etiquetas disponibles -->
              <option value="1">Computacion</option>
              <option value="2">Biblioteca</option>
              <option value="3">Taller</option>
              <option value="4">Espacio digital</option>
              <option value="5">Laboratorio Fisico-quimica</option>
            </select>
            <button onclick="asignarEtiqueta()">Guardar</button>
          </div>
      </div>
    </div>
  </div>
<?php
}
?>