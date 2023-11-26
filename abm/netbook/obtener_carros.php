<?php
$config = include('../db.php');
$conexion = conexion();

if(isset($_POST['areaID'])) {
    $areaID = $_POST['areaID'];
    
    $query = $conexion->prepare("SELECT tipo_recurso_id, tipo_recurso_nombre FROM tipo_recurso WHERE tipo_recurso_area = ?");
    $query->execute([$areaID]);

    $carros = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(array_column($carros, 'tipo_recurso_nombre', 'tipo_recurso_id'));
}
?>
