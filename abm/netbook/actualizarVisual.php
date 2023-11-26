<?php
try {
    include '../funciones.php';
    $config = include('../db.php');

    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $carroID = $_POST["carroID"];





    $sql = "
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
WHERE tipo_recurso.tipo_recurso_id = :carroID
ORDER BY recurso.recurso_id

";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(":carroID", $carroID, PDO::PARAM_INT);
    $stmt->execute();
    $recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_clean(); // Limpia el búfer de salida
    header('Content-Type: application/json');
    echo json_encode(['recursos' => $recursos, 'carroID' => $carroID]);
} catch (Exception $e) {
    // Esto enviará una respuesta con un código de estado 500 y el mensaje de error
    http_response_code(500);
    error_log('Error en actualizarVisual.php: ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
