<?php
$host = 'localhost';
$db = 'bdwebet29';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die();
}

$error = false;
$config = include('../db.php');
$conexion = conexion();
$areas = [];

if ($conexion) {
    $statement = $conexion->prepare("SELECT id, area_nombre FROM area");
    $statement->execute();
    $areas = $statement->fetchAll();
} else {
    echo "Error: No se pudo conectar a la base de datos.";
}

$stmt = $pdo->query("
SELECT 
    recurso.*, 
    IF(
        (registros.opcion = 'Accepted' AND registros.devuelto IN ('Denied', 'Pending')), 
        'Ocupado', 
        'Libre'
    ) as recurso_estado, 
    IF(
        (registros.opcion = 'Accepted' AND registros.devuelto IN ('Denied', 'Pending')), 
        users.user_name, 
        'N/A'
    ) as user_name
FROM recurso 
LEFT JOIN registros 
    ON recurso.recurso_id = registros.idrecurso 
    AND registros.idregistro = (
        SELECT MAX(idregistro) 
        FROM registros AS r
        WHERE r.idrecurso = recurso.recurso_id
    )
LEFT JOIN users 
    ON registros.idusuario = users.user_id 
    left join tipo_recurso
    ON recurso.recurso_tipo = tipo_recurso.tipo_recurso_id
WHERE tipo_recurso.tipo_recurso_id = 1
ORDER BY recurso.recurso_id
");






?>

<?php include "../template/header.php"; ?>



<div style='display: flex; flex-wrap:wrap; justify-content:center; height: 50%; align-items:center; width:100%; flex-direction:column;'>
    <div class="selects">
        <select name="area" id="area">
            <option value="" selected hidden disabled>Selecciona un area</option>
            <?php foreach ($areas as $area) : ?>
                <option value="<?= $area['id'] ?>" class="input"><?= $area['area_nombre'] ?></option>
            <?php endforeach ?>
        </select>
        <select name="carro" id="carro" style="display: none;">
        </select>
    </div>
    <div id="netbookContainer" style='display: flex; flex-wrap: wrap; width: 500px;'>
        <?php
        while ($row = $stmt->fetch()) {
            $color = $row['recurso_estado'] == 'Libre' ? '#d4edda' : '#f8d7da';
            echo "<div class='netbook' 
                     data-recurso_id='{$row['recurso_id']}' 
                     data-recurso_nombre='{$row['recurso_nombre']}' 
                     data-recurso_estado='{$row['recurso_estado']}' 
                     data-reservado-por='{$row['user_name']}' 
                     style='background-color: {$color}; width: 50px; height: 50px; margin: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.15); display: flex; justify-content: center; align-items: center;'>
                    <img src='netbook.png' alt='Netbook' style='width: 50%;'>
                    <p>{$row['recurso_nombre']}</p>
                  </div>";
        }

        ?>
    </div>
    <div id='myModal' class='modal'>
        <div class='modal-content' style="width: 500px;">
            <span class='close'>&times;</span>
            <p id='modal-text'>Some text in the Modal..</p>
        </div>
    </div>
</div>